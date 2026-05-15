<?php

declare(strict_types=1);

namespace Creatuity\AIContentOpenAI\Test\Unit\Model;

use Creatuity\AIContentOpenAI\Model\Config\OpenAiConfig;
use Creatuity\AIContentOpenAI\Model\OpenAiModelList;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class OpenAiModelListTest extends TestCase
{
    private readonly OpenAiConfig|MockObject $openAiConfig;
    private readonly Curl|MockObject $curl;
    private readonly Json|MockObject $json;
    private readonly LoggerInterface|MockObject $logger;

    protected function setUp(): void
    {
        $this->openAiConfig = $this->createMock(OpenAiConfig::class);
        $this->curl = $this->createMock(Curl::class);
        $this->json = $this->createMock(Json::class);
        $this->logger = $this->createMock(LoggerInterface::class);
    }

    public function testReturnsNoModelsWhenApiFetchIsDisabled(): void
    {
        $this->openAiConfig->expects($this->once())->method('shouldFetchModelList')->willReturn(false);
        $this->curl->expects($this->never())->method('get');

        $this->assertSame([], $this->getModelList()->getChatModelIds());
    }

    public function testReturnsNoModelsWhenApiKeyIsEmpty(): void
    {
        $this->openAiConfig->expects($this->once())->method('shouldFetchModelList')->willReturn(true);
        $this->openAiConfig->expects($this->once())->method('getOpenAiApiKey')->willReturn('');
        $this->curl->expects($this->never())->method('get');

        $this->assertSame([], $this->getModelList()->getChatModelIds());
    }

    public function testReturnsFilteredModelIds(): void
    {
        $this->openAiConfig->expects($this->once())->method('shouldFetchModelList')->willReturn(true);
        $this->openAiConfig->expects($this->once())->method('getOpenAiApiKey')->willReturn('api-key');
        $this->curl->expects($this->once())->method('setTimeout')->with(5);
        $this->curl->expects($this->once())->method('setHeaders')->with($this->arrayHasKey('Authorization'));
        $this->curl->expects($this->once())->method('get')->with('https://api.openai.com/v1/models');
        $this->curl->expects($this->once())->method('getStatus')->willReturn(200);
        $this->curl->expects($this->once())->method('getBody')->willReturn('json');
        $this->json->expects($this->once())->method('unserialize')->with('json')->willReturn([
            'data' => [
                ['id' => 'gpt-5.4-mini'],
                ['id' => 'gpt-5.5'],
                ['id' => 'gpt-image-2'],
                ['id' => 'text-embedding-3-large'],
                ['id' => 'ft:gpt-5.4-mini:example:catalog-copy:abc123'],
            ],
        ]);

        $this->assertSame([
            'ft:gpt-5.4-mini:example:catalog-copy:abc123',
            'gpt-5.4-mini',
            'gpt-5.5',
        ], $this->getModelList()->getChatModelIds());
    }

    public function testReturnsNoModelsAndLogsSanitizedWarningWhenModelFetchFails(): void
    {
        $this->openAiConfig->expects($this->once())->method('shouldFetchModelList')->willReturn(true);
        $this->openAiConfig->expects($this->once())->method('getOpenAiApiKey')->willReturn('api-key');
        $this->curl->expects($this->once())->method('setTimeout')->with(5);
        $this->curl->expects($this->once())->method('setHeaders')->with($this->arrayHasKey('Authorization'));
        $this->curl->expects($this->once())
            ->method('get')
            ->with('https://api.openai.com/v1/models')
            ->willThrowException(new \RuntimeException('secret api-key'));
        $this->logger->expects($this->once())
            ->method('warning')
            ->with(
                'Unable to fetch OpenAI model list for admin configuration.',
                $this->callback(function (array $context): bool {
                    $encodedContext = json_encode($context, JSON_THROW_ON_ERROR);

                    return ($context['exception_class'] ?? null) === \RuntimeException::class
                        && array_key_exists('exception_code', $context)
                        && !array_key_exists('exception', $context)
                        && !str_contains($encodedContext, 'api-key');
                })
            );

        $this->assertSame([], $this->getModelList()->getChatModelIds());
    }

    private function getModelList(): OpenAiModelList
    {
        return new OpenAiModelList(
            $this->openAiConfig,
            $this->curl,
            $this->json,
            $this->logger
        );
    }
}
