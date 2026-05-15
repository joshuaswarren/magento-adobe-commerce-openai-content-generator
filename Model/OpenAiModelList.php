<?php

declare(strict_types=1);

namespace Creatuity\AIContentOpenAI\Model;

use Creatuity\AIContentOpenAI\Model\Config\OpenAiConfig;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;

class OpenAiModelList
{
    private const MODELS_ENDPOINT = 'https://api.openai.com/v1/models';
    private const REQUEST_TIMEOUT = 5;
    private const EXCLUDED_MODEL_PARTS = [
        'audio',
        'dall-e',
        'embedding',
        'image',
        'moderation',
        'realtime',
        'search',
        'transcribe',
        'tts',
        'whisper',
    ];

    public function __construct(
        private readonly OpenAiConfig $openAiConfig,
        private readonly Curl $curl,
        private readonly Json $json,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * @return string[]
     */
    public function getChatModelIds(): array
    {
        if (!$this->openAiConfig->shouldFetchModelList()) {
            return [];
        }

        $apiKey = $this->openAiConfig->getOpenAiApiKey();
        if ($apiKey === '') {
            return [];
        }

        try {
            $this->curl->setTimeout(self::REQUEST_TIMEOUT);
            $this->curl->setHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ]);
            $this->curl->get(self::MODELS_ENDPOINT);

            if ($this->curl->getStatus() >= 400) {
                return [];
            }

            $response = $this->json->unserialize($this->curl->getBody());
            $models = [];
            foreach (($response['data'] ?? []) as $model) {
                $id = (string)($model['id'] ?? '');
                if ($this->isLikelyChatModel($id)) {
                    $models[] = $id;
                }
            }

            $models = array_values(array_unique($models));
            sort($models);

            return $models;
        } catch (\Throwable $exception) {
            $this->logger->warning(
                'Unable to fetch OpenAI model list for admin configuration.',
                [
                    'exception_class' => $exception::class,
                    'exception_code' => $exception->getCode(),
                ]
            );

            return [];
        }
    }

    private function isLikelyChatModel(string $modelId): bool
    {
        if ($modelId === '') {
            return false;
        }

        foreach (self::EXCLUDED_MODEL_PARTS as $excludedPart) {
            if (str_contains($modelId, $excludedPart)) {
                return false;
            }
        }

        return preg_match('/^(ft:)?(gpt-|o[0-9])/', $modelId) === 1;
    }
}
