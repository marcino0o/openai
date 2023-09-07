<?php

declare(strict_types=1);

namespace RWS\Openai;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use RWS\Openai\Exception\LimitExceededException;
use RWS\Openai\Exception\OpenAIClientException;
use RWS\Openai\Exception\UnauthorizedException;

class OpenAIHTTPClient extends Client
{
    private const API_URL = 'https://api.openai.com/';

    public function __construct(string $apiKey, array $config = [])
    {
        parent::__construct(
            array_merge(
                $config,
                [
                    'base_uri' => self::API_URL,
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => sprintf('Bearer %s', $apiKey),
                    ],
                ]
            )
        );
    }

    /**
     * @throws UnauthorizedException
     * @throws LimitExceededException
     * @throws OpenAIClientException
     */
    public function postJson($uri, array $body, array $options = []): ResponseInterface
    {
        try {
            return $this->post($uri, array_merge(['json' => $body], $options));
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
