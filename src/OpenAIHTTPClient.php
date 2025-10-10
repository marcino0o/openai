<?php

declare(strict_types=1);

namespace Openai;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Openai\Exception\LimitExceededException;
use Openai\Exception\OpenAIClientException;
use Openai\Exception\UnauthorizedException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

class OpenAIHTTPClient extends Client
{
    private const string API_URL = 'https://api.openai.com/';

    /**
     * @param array<string, string> $config
     */
    public function __construct(string $apiKey, array $config = [])
    {
        parent::__construct(
            array_merge(
                $config,
                [
                    'base_uri' => self::API_URL,
                    'headers' => [
                        'Authorization' => sprintf('Bearer %s', $apiKey),
                    ],
                ]
            )
        );
    }

    /**
     * @param array<string, mixed> $body
     * @param array<string, mixed> $options
     *
     * @return ResponseInterface
     * @throws LimitExceededException
     * @throws OpenAIClientException
     * @throws UnauthorizedException
     */
    public function postData(UriInterface|string $uri, array $body, string $contentType = 'json', array $options = []): ResponseInterface
    {
        try {
            return $this->post($uri, array_merge([
                $contentType => $body,
            ], $options));
        } catch (ClientException $e) {
            throw match ($e->getResponse()->getStatusCode()) {
                429 => LimitExceededException::fromPrevious($e),
                401 => UnauthorizedException::fromPrevious($e),
                default => OpenAIClientException::fromPrevious($e),
            };
        } catch (GuzzleException $e) {
            throw OpenAIClientException::fromPrevious($e);
        }
    }
}
