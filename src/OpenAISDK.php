<?php

declare(strict_types=1);

namespace Openai;

use GuzzleHttp\Psr7\Utils;
use Openai\Audio\TranscriptionResponse;
use Openai\Chat\ChatCompletionResponse;
use Openai\Chat\Message;
use Openai\Chat\Messages;
use Openai\Exception\OpenAIClientException;
use Openai\Image\ImageResponse;
use Openai\Image\Prompt;
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
        ?int $temperature = 1,
        ?int $choices = null,
        ?string $user = null
    ): ChatCompletionResponse {
        $request = [
            'model' => $model->value,
            'temperature' => $temperature,
            'messages' => $messages->map(
                static fn (Message $message): array => [
                    'role' => $message->role->value,
                    'content' => $message->content,
                ]
            ),
        ];

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
    public function createTranscription(string $filePath): TranscriptionResponse
    {
        $rawResponse = $this->client->postData(
            self::TRANSCRIPTIONS_PATH,
            [[
                'file' => Utils::tryFopen($filePath,'r'),
                'model' => Model::WHISPER1,
            ]],
            contentType: 'multipart'
        );

        return TranscriptionResponse::fromJson($rawResponse->getBody()->getContents());
    }

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
