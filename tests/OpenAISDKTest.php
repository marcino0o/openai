<?php

declare(strict_types=1);

namespace RWS\Openai\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use RWS\Openai\Chat\ChatCompletionResponse;
use RWS\Openai\Chat\Message;
use RWS\Openai\Chat\Messages;
use RWS\Openai\Configuration;
use RWS\Openai\OpenAIHTTPClient;
use RWS\Openai\OpenAISDK;

class OpenAISDKTest extends TestCase
{
    /**
     * @test
     */
    public function shouldCreateChatCompletion(): void
    {
        $sut = new OpenAISDK(
            $this->createClientMockWithResponse(
                '{"id": "chatcmpl-123","object": "chat.completion","created": 1677652288,"model": "gpt-3.5-turbo","choices": [{"index": 0,"message": {"role": "assistant","content": "\n\nHello there, how may I assist you today?"},"finish_reason": "stop"}],"usage": {"prompt_tokens": 9,"completion_tokens": 12,"total_tokens": 21}}'
            )
        );

        /** @noinspection PhpUnhandledExceptionInspection */
        $response = $sut->createChatCompletion(
            new Messages(
                Message::fromSystem('You are very rude, cowboy style talking old man.'),
                Message::fromUser('Hello'),
            )
        );

        $this->assertInstanceOf(ChatCompletionResponse::class, $response);
    }

    /**
     * @test
     */
    public function shouldCreateModeration(): void
    {
        $sut = new OpenAISDK(
            $this->createClientMockWithResponse(
                '{"id":"modr-123","model":"text-moderation-006","results":[{"flagged":true,"categories":{"sexual":false,"hate":false,"harassment":true,"self-harm":false,"sexual\/minors":false,"hate\/threatening":false,"violence\/graphic":false,"self-harm\/intent":false,"self-harm\/instructions":false,"harassment\/threatening":true,"violence":true},"category_scores":{"sexual":8.795877e-5,"hate":0.093559496,"harassment":0.8825318,"self-harm":6.0274e-7,"sexual\/minors":6.683881e-7,"hate\/threatening":0.0021309461,"violence\/graphic":8.4004405e-8,"self-harm\/intent":5.021167e-8,"self-harm\/instructions":6.528286e-11,"harassment\/threatening":0.9360076,"violence":0.9976956}}]}'
            )
        );


        /** @noinspection PhpUnhandledExceptionInspection */
        $response = $sut->createModeration('I\'m gonna kill that b*tch');

        $this->assertTrue($response->moderationResult->flagged);
    }

    private function createClientMockWithResponse(
        string $body,
        int $status = 200,
        array $headers = []
    ): OpenAIHTTPClient {
        $mock = new MockHandler([
            new Response($status, $headers, $body),
        ]);

        $handlerStack = HandlerStack::create($mock);

        return new OpenAIHTTPClient(
            new Configuration('api_key', 'organization_id'),
            ['handler' => $handlerStack]
        );
    }
}