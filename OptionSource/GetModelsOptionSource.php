<?php

declare(strict_types=1);

namespace Creatuity\AIContentOpenAI\OptionSource;

use Creatuity\AIContentOpenAI\Model\Config\OpenAiConfig;
use Creatuity\AIContentOpenAI\Model\GetModelSettings;
use Creatuity\AIContentOpenAI\Model\OpenAiModelList;
use Magento\Framework\Data\OptionSourceInterface;

class GetModelsOptionSource implements OptionSourceInterface
{
    public function __construct(
        private readonly GetModelSettings $modelSettings,
        private readonly OpenAiModelList $openAiModelList
    ) {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function toOptionArray(): array
    {
        $models = array_values(array_unique(array_merge(
            $this->modelSettings->getModelNames(),
            $this->openAiModelList->getChatModelIds()
        )));
        sort($models);

        $options = array_map(function ($model) {
            return ['label' => $model, 'value' => $model];
        }, $models);

        $options[] = [
            'label' => (string) __('Custom model ID'),
            'value' => OpenAiConfig::CUSTOM_MODEL_VALUE
        ];

        return $options;
    }
}
