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
use Openai\Exception\OpenAIClientException;
use Openai\Image\ImageResponse;
use Openai\Image\ResponseFormat;
use Openai\Image\Size;
use Openai\Moderation\ModerationResponse;

readonly class OpenAISDK
{
    private const CHAT_COMPLETIONS_PATH = '/v1/chat/completions';
    private const MODERATIONS_PATH = '/v1/moderations';
    private const TRANSCRIPTIONS_PATH = '/v1/audio/transcriptions';
    private const IMAGES_GENERATIONS_PATH = '/v1/images/generations';

    public function __construct(
        private OpenAIHTTPClient $client
    ) {
    }

    /**
     * @throws OpenAIClientException
     */
    public function createChatCompletion(
        Messages $messages,
        Model $model = Model::GPT3_5_TURBO,
        ?FrequencyPenalty $frequencyPenalty = null,
        ?ChatTemperature $temperature = null,
        ?int $choices = null,
        ?string $user = null
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
                'model' => $model->value
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
            [
                'name' => 'file',
                'contents' => Utils::tryFopen($filePath,'r'),
            ],
            [
                'name' => 'model',
                'contents' => Model::WHISPER1->value,
            ]
        ];

        if ($temperature !== null) {
            $request[] = [
                'name' => 'temperature',
                'contents' => $temperature->value,
            ];
        }

        if ($language !== null) {
            $request[] = [
                'name' => 'language',
                'contents' => $language->value,
            ];
        }

        if ($prompt !== null) {
            $request[] = [
                'name' => 'prompt',
                'contents' => $prompt->text,
            ];
        }

        if ($responseFormat !== null) {
            $request[] = [
                'name' => 'response_format',
                'contents' => $responseFormat->value,
            ];
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
    public function createAudioTranslation(
        string $filePath,
        ?AudioTemperature $temperature = null,
        ?Prompt $prompt = null,
        ?ResponseFormat $responseFormat = null
    ): TranscriptionResponse {
        $request = [
            [
                'name' => 'file',
                'contents' => Utils::tryFopen($filePath,'r'),
            ],
            [
                'name' => 'model',
                'contents' => Model::WHISPER1->value,
            ]
        ];

        if ($temperature !== null) {
            $request[] = [
                'name' => 'temperature',
                'contents' => $temperature->value,
            ];
        }

        if ($prompt !== null) {
            $request[] = [
                'name' => 'prompt',
                'contents' => $prompt->text,
            ];
        }

        if ($responseFormat !== null) {
            $request[] = [
                'name' => 'response_format',
                'contents' => $responseFormat->value,
            ];
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
            'response_format' => $responseFormat->value
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
}
