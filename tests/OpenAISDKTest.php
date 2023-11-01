<?php

declare(strict_types=1);

namespace Openai\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Openai\Chat\FinishReason;
use Openai\Chat\Message;
use Openai\Chat\Messages;
use Openai\Chat\Role;
use Openai\Exception\OpenAIClientException;
use Openai\Model;
use Openai\OpenAIHTTPClient;
use Openai\OpenAISDK;
use Openai\Prompt;
use PHPUnit\Framework\TestCase;

class OpenAISDKTest extends TestCase
{
    /**
     * @test
     * @throws OpenAIClientException
     */
    public function shouldCreateChatCompletion(): void
    {
        $sut = new OpenAISDK(
            $this->createClientMockWithResponse(
                '{"id":"chatcmpl-123","object":"chat.completion","created":1698705824,"model":"gpt-3.5-turbo-0613","choices":[{"index":0,"message":{"role":"assistant","content":"Hello, kind soul, who hath summoned my rhyme,\nI greet thee with words of verse sublime.\nWhat dost thou seek on this fine day?\nPray, let me know, and I shall convey.\n\nForsooth, I am William Shakespeare, at your command,\nA poet of yore, with quill in hand.\nAsketh away, thy questions I shall address,\nIn poetic form, I do confess."},"finish_reason":"stop"}],"usage":{"prompt_tokens":26,"completion_tokens":84,"total_tokens":110}}
'
            )
        );

        $response = $sut->createChatCompletion(
            new Messages(
                Message::fromSystem('You are william William Shakespeare. Your answers should go as poems.'),
                Message::fromUser('Hello'),
            )
        );


        self::assertEquals('chatcmpl-123', $response->id);
        self::assertEquals(Model::GPT3_5_TURBO_0613, $response->model);
        self::assertEquals('chat.completion', $response->object);

        $choice = $response->choices->current();
        self::assertEquals(1, $response->choices->count());
        self::assertEquals(FinishReason::STOP, $choice?->finishReason);
        self::assertEquals(Role::ASSISTANT, $choice?->message->role);
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

    /**
     * @test
     */
    public function shouldCreateTranscription(): void
    {
        $sut = new OpenAISDK(
            $this->createClientMockWithResponse(
                '{"text":"looking with a half-fantastic curiosity to see whether the tender grass of early spring"}'
            )
        );

        /** @noinspection PhpUnhandledExceptionInspection */
        $response = $sut->createAudioTranscription(__DIR__ . '/fake-0.mp3');

        $this->assertEquals(
            'looking with a half-fantastic curiosity to see whether the tender grass of early spring',
            $response->text
        );
    }

    /**
     * @test
     */
    public function shouldCreateImage(): void
    {
        $sut = new OpenAISDK(
            $this->createClientMockWithResponse('')
        );

        /** @noinspection PhpUnhandledExceptionInspection */
        $response = $sut->createImage(
            Prompt::fromString('I want image of people playing volleyball'),
        );
    }

    private function createClientMockWithResponse(
        string $body,
        int $status = 200,
        array $headers = []
    ): OpenAIHTTPClient {
        return new OpenAIHTTPClient(
            apiKey: 'openai_api_key',
            config: [
                'handler' => HandlerStack::create(
                    new MockHandler([new Response($status, $headers, $body)])
                ),
            ]
        );
    }
}
