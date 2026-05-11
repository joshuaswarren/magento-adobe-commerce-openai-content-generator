<?php

declare(strict_types=1);

namespace Creatuity\AIContentOpenAI\Test\Unit\OptionSource;

use Creatuity\AIContentOpenAI\Model\Config\OpenAiConfig;
use Creatuity\AIContentOpenAI\Model\GetModelSettings;
use Creatuity\AIContentOpenAI\Model\OpenAiModelList;
use Creatuity\AIContentOpenAI\OptionSource\GetModelsOptionSource;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GetModelsOptionSourceTest extends TestCase
{
    public function testToOptionArrayMergesStaticApiAndCustomModels(): void
    {
        /** @var GetModelSettings|MockObject $modelSettings */
        $modelSettings = $this->createMock(GetModelSettings::class);
        /** @var OpenAiModelList|MockObject $openAiModelList */
        $openAiModelList = $this->createMock(OpenAiModelList::class);

        $modelSettings->expects($this->once())
            ->method('getModelNames')
            ->willReturn(['gpt-5.4-mini', 'gpt-5.5']);
        $openAiModelList->expects($this->once())
            ->method('getChatModelIds')
            ->willReturn(['gpt-5.5', 'ft:gpt-5.4-mini:example:catalog-copy:abc123']);

        $this->assertSame([
            ['label' => 'ft:gpt-5.4-mini:example:catalog-copy:abc123', 'value' => 'ft:gpt-5.4-mini:example:catalog-copy:abc123'],
            ['label' => 'gpt-5.4-mini', 'value' => 'gpt-5.4-mini'],
            ['label' => 'gpt-5.5', 'value' => 'gpt-5.5'],
            ['label' => 'Custom model ID', 'value' => OpenAiConfig::CUSTOM_MODEL_VALUE],
        ], (new GetModelsOptionSource($modelSettings, $openAiModelList))->toOptionArray());
    }
}
