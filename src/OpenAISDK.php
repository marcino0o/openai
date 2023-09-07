<?php

declare(strict_types=1);

namespace RWS\Openai;

use RWS\Openai\Chat\ChatCompletionResponse;
use RWS\Openai\Chat\Message;
use RWS\Openai\Chat\Messages;
use RWS\Openai\Exception\OpenAIClientException;
use RWS\Openai\Moderation\ModerationResponse;

readonly class OpenAISDK
{
    private const CHAT_COMPLETIONS_PATH = '/v1/chat/completions';
    private const MODERATIONS_PATH = '/v1/moderations';

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
        ?int $choices = null
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

        $rawResponse = $this->client->postJson(self::CHAT_COMPLETIONS_PATH, $request);

        return ChatCompletionResponse::fromJson($rawResponse->getBody()->getContents());
    }

    /**
     * @throws OpenAIClientException
     */
    public function createModeration(string $input, Model $model = Model::MODERATION_LATEST): ModerationResponse
    {
        $rawResponse = $this->client->postJson(
            self::MODERATIONS_PATH,
            [
                'input' => $input,
                'model' => $model->value
            ]
        );

        return ModerationResponse::fromJson($rawResponse->getBody()->getContents());
    }
}