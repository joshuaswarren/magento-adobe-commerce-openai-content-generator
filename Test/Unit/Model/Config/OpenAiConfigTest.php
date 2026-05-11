<?php

declare(strict_types=1);

namespace Creatuity\AIContentOpenAI\Test\Unit\Model\Config;

use Creatuity\AIContentOpenAI\Model\Config\OpenAiConfig;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class OpenAiConfigTest extends TestCase
{
    private readonly ScopeConfigInterface|MockObject $scopeConfig;
    private readonly EncryptorInterface|MockObject $encryptor;

    public function setUp(): void
    {
        $this->scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $this->encryptor = $this->createMock(EncryptorInterface::class);
    }

    public function testGetOpenAiApiKey(): void
    {
        $key = 'some key';
        $decryptedKey = 'decrypted key';
        $this->scopeConfig->expects($this->once())->method('getValue')->with(OpenAiConfig::XML_PATH_OPENAI_API_KEY)->willReturn($key);
        $this->encryptor->expects($this->once())->method('decrypt')->with($key)->willReturn($decryptedKey);
        $this->assertSame($decryptedKey, (new OpenAiConfig($this->scopeConfig, $this->encryptor))->getOpenAiApiKey());
    }

    public function testGetModelName(): void
    {
        $model = 'some model name';
        $this->scopeConfig->expects($this->once())->method('getValue')->with(OpenAiConfig::XML_PATH_OPENAI_MODEL_NAME)->willReturn($model);
        $this->assertSame($model, (new OpenAiConfig($this->scopeConfig, $this->encryptor))->getModelName());
    }

    public function testGetModelNameFallsBackToDefault(): void
    {
        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(OpenAiConfig::XML_PATH_OPENAI_MODEL_NAME)
            ->willReturn('');

        $this->assertSame(
            OpenAiConfig::DEFAULT_MODEL,
            (new OpenAiConfig($this->scopeConfig, $this->encryptor))->getModelName()
        );
    }

    public function testGetModelNameUsesCustomModel(): void
    {
        $customModel = 'ft:gpt-5.4-mini:example:catalog-copy:abc123';
        $this->scopeConfig->expects($this->exactly(2))
            ->method('getValue')
            ->willReturnCallback(function (string $path) use ($customModel) {
                return match ($path) {
                    OpenAiConfig::XML_PATH_OPENAI_MODEL_NAME => OpenAiConfig::CUSTOM_MODEL_VALUE,
                    OpenAiConfig::XML_PATH_OPENAI_CUSTOM_MODEL_NAME => $customModel,
                    default => '',
                };
            });

        $this->assertSame($customModel, (new OpenAiConfig($this->scopeConfig, $this->encryptor))->getModelName());
    }

    public function testGetModelNameFallsBackWhenCustomModelIsEmpty(): void
    {
        $this->scopeConfig->expects($this->exactly(2))
            ->method('getValue')
            ->willReturnCallback(function (string $path) {
                return match ($path) {
                    OpenAiConfig::XML_PATH_OPENAI_MODEL_NAME => OpenAiConfig::CUSTOM_MODEL_VALUE,
                    OpenAiConfig::XML_PATH_OPENAI_CUSTOM_MODEL_NAME => '',
                    default => '',
                };
            });

        $this->assertSame(
            OpenAiConfig::DEFAULT_MODEL,
            (new OpenAiConfig($this->scopeConfig, $this->encryptor))->getModelName()
        );
    }

    public function testShouldFetchModelList(): void
    {
        $this->scopeConfig->expects($this->once())
            ->method('isSetFlag')
            ->with(OpenAiConfig::XML_PATH_OPENAI_FETCH_MODEL_LIST)
            ->willReturn(true);

        $this->assertTrue((new OpenAiConfig($this->scopeConfig, $this->encryptor))->shouldFetchModelList());
    }
}
