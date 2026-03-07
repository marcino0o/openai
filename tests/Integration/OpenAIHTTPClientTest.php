<?php

declare(strict_types=1);

namespace Openai\Tests\Integration;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Openai\Exception\LimitExceededException;
use Openai\Exception\OpenAIClientException;
use Openai\Exception\UnauthorizedException;
use Openai\OpenAIHTTPClient;
use PHPUnit\Framework\TestCase;

class OpenAIHTTPClientTest extends TestCase
{
    /**
     * @test
     */
    public function shouldMap401ToUnauthorizedException(): void
    {
        $client = $this->createClientWithQueue(new Response(401, [], '{}'));

        $this->expectException(UnauthorizedException::class);
        $client->postData('/v1/chat/completions', [
            'model' => 'gpt-5',
        ]);
    }

    /**
     * @test
     */
    public function shouldMap429ToLimitExceededException(): void
    {
        $client = $this->createClientWithQueue(new Response(429, [], '{}'));

        $this->expectException(LimitExceededException::class);
        $client->postData('/v1/chat/completions', [
            'model' => 'gpt-5',
        ]);
    }

    /**
     * @test
     */
    public function shouldMapTransportExceptionToGenericClientException(): void
    {
        $request = new Request('POST', '/v1/chat/completions');
        $connectException = new ConnectException('Connection failed', $request);
        $client = $this->createClientWithQueue($connectException);

        $this->expectException(OpenAIClientException::class);
        $client->postData('/v1/chat/completions', [
            'model' => 'gpt-5',
        ]);
    }

    private function createClientWithQueue(mixed $result): OpenAIHTTPClient
    {
        return new OpenAIHTTPClient(
            apiKey: 'openai_api_key',
            config: [
                'handler' => HandlerStack::create(new MockHandler([$result])),
            ]
        );
    }
}
