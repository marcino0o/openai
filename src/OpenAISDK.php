<?php

declare(strict_types=1);

namespace Openai;

use GuzzleHttp\Psr7\Utils;
use Openai\Audio\AudioResponseFormat;
use Openai\Audio\AudioTemperature;
use Openai\Audio\Language;
use Openai\Audio\TranscriptionResponse;
use Openai\Chat\ChatCompletionResponse;
use Openai\Chat\ChatTemperature;
use Openai\Chat\FrequencyPenalty;
use Openai\Chat\Message;
use Openai\Chat\Messages;
use Openai\Chat\PresencePenalty;
use Openai\Chat\ReasoningEffort;
use Openai\Chat\TopP;
use Openai\Exception\OpenAIClientException;
use Openai\Image\ImageResponse;
use Openai\Image\ResponseFormat;
use Openai\Image\Size;
use Openai\Moderation\ModerationResponse;
use Openai\Utils\RequestUtils;

final readonly class OpenAISDK
{
    private const string CHAT_COMPLETIONS_PATH = '/v1/chat/completions';
    private const string MODERATIONS_PATH = '/v1/moderations';
    private const string TRANSCRIPTIONS_PATH = '/v1/audio/transcriptions';
    private const string IMAGES_GENERATIONS_PATH = '/v1/images/generations';

    public function __construct(
        private OpenAIHTTPClient $client
    ) {
    }

    /**
     * @throws OpenAIClientException
     */
    public function createChatCompletion(
        Messages $messages,
        Model $model = Model::GPT5,
        ?FrequencyPenalty $frequencyPenalty = null,
        ?ChatTemperature $temperature = null,
        ?int $choices = null,
        ?string $user = null,
        ?PresencePenalty $presencePenalty = null,
        ?TopP $topP = null,
        ?int $maxCompletionTokens = null,
        array|string|null $stop = null,
        ?ReasoningEffort $reasoningEffort = null
    ): ChatCompletionResponse {
        $request = [
            'model' => $model->value,
            'messages' => $messages->map(
                static fn (Message $message): array => [
                    'role' => $message->role->value,
                    'content' => $message->content,
                ]
            ),
        ];

        if ($frequencyPenalty !== null) {
            $request['frequency_penalty'] = $frequencyPenalty->value;
        }

        if ($temperature !== null) {
            $request['temperature'] = $temperature->value;
        }

        if ($choices !== null) {
            $request['n'] = $choices;
        }

        if ($user !== null) {
            $request['user'] = $user;
        }

        if ($presencePenalty !== null) {
            $request['presence_penalty'] = $presencePenalty->value;
        }

        if ($topP !== null) {
            $request['top_p'] = $topP->value;
        }

        if ($maxCompletionTokens !== null) {
            if ($maxCompletionTokens <= 0) {
                throw new \InvalidArgumentException('maxCompletionTokens must be greater than 0.');
            }

            $request['max_completion_tokens'] = $maxCompletionTokens;
        }

        if ($stop !== null) {
            $request['stop'] = $stop;
        }

        if ($reasoningEffort !== null) {
            $request['reasoning_effort'] = $reasoningEffort->value;
        }

        $rawResponse = $this->client->postData(self::CHAT_COMPLETIONS_PATH, $request);

        return ChatCompletionResponse::fromJson($rawResponse->getBody()->getContents());
    }

    /**
     * @throws OpenAIClientException
     */
    public function createModeration(string $input, Model $model = Model::MODERATION_LATEST): ModerationResponse
    {
        $rawResponse = $this->client->postData(
            self::MODERATIONS_PATH,
            [
                'input' => $input,
                'model' => $model->value,
            ]
        );

        return ModerationResponse::fromJson($rawResponse->getBody()->getContents());
    }

    /**
     * @throws OpenAIClientException
     */
    public function createAudioTranscription(
        string $filePath,
        ?AudioTemperature $temperature = null,
        ?Language $language = null,
        ?Prompt $prompt = null,
        ?AudioResponseFormat $responseFormat = null
    ): TranscriptionResponse {
        $request = [
            RequestUtils::buildRequestPart('file', Utils::tryFopen($filePath, 'r')),
            RequestUtils::buildRequestPart('model', Model::WHISPER1->value),
        ];

        if ($temperature !== null) {
            $request[] = RequestUtils::buildRequestPart('temperature', $temperature->value);
        }

        if ($language !== null) {
            $request[] = RequestUtils::buildRequestPart('language', $language->value);
        }

        if ($prompt !== null) {
            $request[] = RequestUtils::buildRequestPart('prompt', $prompt->text);
        }

        if ($responseFormat !== null) {
            $request[] = RequestUtils::buildRequestPart('response_format', $responseFormat->value);
        }

        $rawResponse = $this->client->postData(
            uri: self::TRANSCRIPTIONS_PATH,
            body: $request,
            contentType: 'multipart'
        );

        return TranscriptionResponse::fromJson($rawResponse->getBody()->getContents());
    }

    /**
     * @throws OpenAIClientException
     */
    public function createAudioTranslation(
        string $filePath,
        ?AudioTemperature $temperature = null,
        ?Prompt $prompt = null,
        ?ResponseFormat $responseFormat = null
    ): TranscriptionResponse {
        $request = [
            RequestUtils::buildRequestPart('file', Utils::tryFopen($filePath, 'r')),
            RequestUtils::buildRequestPart('model', Model::WHISPER1->value),
        ];

        if ($temperature !== null) {
            $request[] = RequestUtils::buildRequestPart('temperature', $temperature->value);
        }

        if ($prompt !== null) {
            $request[] = RequestUtils::buildRequestPart('prompt', $prompt->text);
        }

        if ($responseFormat !== null) {
            $request[] = RequestUtils::buildRequestPart('response_format', $responseFormat->value);
        }

        $rawResponse = $this->client->postData(
            self::TRANSCRIPTIONS_PATH,
            $request,
            contentType: 'multipart'
        );

        return TranscriptionResponse::fromJson($rawResponse->getBody()->getContents());
    }

    /**
     * @throws OpenAIClientException
     */
    public function createImage(
        Prompt $prompt,
        Size $size = Size::LARGE,
        ResponseFormat $responseFormat = ResponseFormat::URL,
        ?int $choices = null,
        ?string $user = null
    ): ImageResponse {
        $request = [
            'prompt' => $prompt->text,
            'size' => $size->value,
            'response_format' => $responseFormat->value,
        ];

        if ($choices !== null) {
            $request['n'] = $choices;
        }

        if ($user !== null) {
            $request['user'] = $user;
        }

        $rawResponse = $this->client->postData(self::IMAGES_GENERATIONS_PATH, $request);

        return ImageResponse::fromJson($rawResponse->getBody()->getContents());
    }

    public function createResponse(): string
    {
        throw new \RuntimeException('Not implemented');
    }
}
