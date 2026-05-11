<?php

declare(strict_types=1);

namespace Creatuity\AIContentOpenAI\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;

class OpenAiConfig
{
    public const DEFAULT_MODEL = 'gpt-5.4-mini';
    public const CUSTOM_MODEL_VALUE = '__custom__';

    public const XML_PATH_OPENAI_API_KEY = 'creatuityaicontent/openai_api/api_key';
    public const XML_PATH_OPENAI_MODEL_NAME = 'creatuityaicontent/openai_api/model_name';
    public const XML_PATH_OPENAI_CUSTOM_MODEL_NAME = 'creatuityaicontent/openai_api/custom_model_name';
    public const XML_PATH_OPENAI_FETCH_MODEL_LIST = 'creatuityaicontent/openai_api/fetch_model_list';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly EncryptorInterface $encryptor
    ) {
    }

    public function getOpenAiApiKey(): string
    {
        $key = (string) $this->scopeConfig->getValue(self::XML_PATH_OPENAI_API_KEY);

        return $this->encryptor->decrypt($key);
    }

    public function getModelName(): string
    {
        $modelName = trim((string) $this->scopeConfig->getValue(self::XML_PATH_OPENAI_MODEL_NAME));
        if ($modelName === self::CUSTOM_MODEL_VALUE) {
            return $this->getCustomModelName() ?: self::DEFAULT_MODEL;
        }

        return $modelName ?: self::DEFAULT_MODEL;
    }

    public function getCustomModelName(): string
    {
        return trim((string) $this->scopeConfig->getValue(self::XML_PATH_OPENAI_CUSTOM_MODEL_NAME));
    }

    public function shouldFetchModelList(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_OPENAI_FETCH_MODEL_LIST);
    }
}
