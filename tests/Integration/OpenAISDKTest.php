<?php

declare(strict_types=1);

namespace Openai\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Openai\Audio\AudioResponseFormat;
use Openai\Chat\FinishReason;
use Openai\Chat\Message;
use Openai\Chat\Messages;
use Openai\Chat\PresencePenalty;
use Openai\Chat\ReasoningEffort;
use Openai\Chat\Role;
use Openai\Chat\TopP;
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
                '{"id":"chatcmpl-123","object":"chat.completion","created":1698705824,"model":"gpt-5","choices":[{"index":0,"message":{"role":"assistant","content":"Hello, kind soul, who hath summoned my rhyme,\nI greet thee with words of verse sublime.\nWhat dost thou seek on this fine day?\nPray, let me know, and I shall convey.\n\nForsooth, I am William Shakespeare, at your command,\nA poet of yore, with quill in hand.\nAsketh away, thy questions I shall address,\nIn poetic form, I do confess."},"finish_reason":"stop"}],"usage":{"prompt_tokens":26,"completion_tokens":84,"total_tokens":110}}
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
        self::assertEquals(Model::GPT5, $response->model);
        self::assertEquals('chat.completion', $response->object);

        $choice = $response->choices->current();
        self::assertEquals(1, $response->choices->count());
        self::assertEquals(FinishReason::STOP, $choice?->finishReason);
        self::assertEquals(Role::ASSISTANT, $choice?->message->role);
    }


    /**
     * @test
     */
    public function shouldPassNewChatCompletionOptionsToRequest(): void
    {
        /** @var array<int, array{request: RequestInterface}> $container */
        $container = [];
        $history = Middleware::history($container);
        $handlerStack = HandlerStack::create(
            new MockHandler([
                new Response(200, [], '{"id":"chatcmpl-123","object":"chat.completion","created":1698705824,"model":"gpt-5","choices":[{"index":0,"message":{"role":"assistant","content":"ok"},"finish_reason":"stop"}],"usage":{"prompt_tokens":1,"completion_tokens":1,"total_tokens":2}}'),
            ])
        );
        $handlerStack->push($history);

        $sut = new OpenAISDK(
            new OpenAIHTTPClient(
                apiKey: 'openai_api_key',
                config: [
                    'handler' => $handlerStack,
                ]
            )
        );

        /** @noinspection PhpUnhandledExceptionInspection */
        $sut->createChatCompletion(
            messages: new Messages(Message::fromUser('Hello')),
            presencePenalty: PresencePenalty::tryFrom(0.3),
            topP: TopP::tryFrom(0.9),
            maxCompletionTokens: 256,
            stop: ['END'],
            reasoningEffort: ReasoningEffort::MEDIUM
        );

        self::assertCount(1, $container);
        $request = $container[0]['request'];
        self::assertInstanceOf(RequestInterface::class, $request);

        /** @var array{presence_penalty: float, top_p: float, max_completion_tokens: int, stop: list<string>, reasoning_effort: string} $payload */
        $payload = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(0.3, $payload['presence_penalty']);
        self::assertSame(0.9, $payload['top_p']);
        self::assertSame(256, $payload['max_completion_tokens']);
        self::assertSame(['END'], $payload['stop']);
        self::assertSame('medium', $payload['reasoning_effort']);
    }


    /**
     * @test
     */
    public function shouldRejectNonPositiveMaxCompletionTokens(): void
    {
        $sut = new OpenAISDK(
            $this->createClientMockWithResponse('{"id":"chatcmpl-123","object":"chat.completion","created":1698705824,"model":"gpt-5","choices":[],"usage":{"prompt_tokens":1,"completion_tokens":1,"total_tokens":2}}')
        );

        $this->expectException(\InvalidArgumentException::class);
        $sut->createChatCompletion(
            messages: new Messages(Message::fromUser('Hello')),
            maxCompletionTokens: 0,
        );
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
        $response = $sut->createAudioTranscription(dirname(__DIR__) . '/fake-0.mp3');

        $this->assertEquals(
            'looking with a half-fantastic curiosity to see whether the tender grass of early spring',
            $response->text
        );
    }

    /**
     * @test
     */
    public function shouldCreateTranslationWithExpectedMultipartPayload(): void
    {
        /** @var array<int, array{request: RequestInterface}> $container */
        $container = [];
        $history = Middleware::history($container);
        $handlerStack = HandlerStack::create(
            new MockHandler([
                new Response(200, [], '{"text":"translated text"}'),
            ])
        );
        $handlerStack->push($history);

        $sut = new OpenAISDK(
            new OpenAIHTTPClient(
                apiKey: 'openai_api_key',
                config: [
                    'handler' => $handlerStack,
                ]
            )
        );

        /** @noinspection PhpUnhandledExceptionInspection */
        $response = $sut->createAudioTranslation(
            filePath: dirname(__DIR__) . '/fake-0.mp3',
            responseFormat: AudioResponseFormat::JSON,
        );

        self::assertSame('translated text', $response->text);
        self::assertCount(1, $container);

        $request = $container[0]['request'];
        self::assertInstanceOf(RequestInterface::class, $request);
        self::assertSame('/v1/audio/translations', $request->getUri()->getPath());
    }

    /**
     * @test
     */
    public function shouldCreateImage(): void
    {
        $sut = new OpenAISDK(
            $this->createClientMockWithResponse('{"created": 1713833628,"data": [{"b64_json": "..."}],"usage": {"total_tokens": 100,"input_tokens": 50,"output_tokens": 50,"input_tokens_details": {"text_tokens": 10,"image_tokens": 40}}}')
        );

        /** @noinspection PhpUnhandledExceptionInspection */
        $response = $sut->createImage(
            Prompt::fromString('I want image of people playing volleyball'),
        );

        $this->assertObjectHasProperty('images', $response);
    }


    /**
     * @test
     */
    public function shouldParseNewModelPrefixes(): void
    {
        self::assertSame(Model::GPT_4_1, Model::tryFromModelString('gpt-4.1-2025-04-14'));
        self::assertSame(Model::O3, Model::tryFromModelString('o3-2025-04-16'));
        self::assertSame(Model::OMNI_MODERATION_LATEST, Model::tryFromModelString('omni-moderation-latest'));
    }

    /**
     * @param array<array<string>|string> $headers
     */
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
