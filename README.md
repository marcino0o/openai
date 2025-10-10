# OpenAI PHP SDK (by @marcino0o)

A lightweight, modern PHP (≥ 8.4) wrapper for the OpenAI API. Focused on simplicity and rapid integration for Chat Completions, with growing support for other endpoints.

---

[![Packagist Version](https://img.shields.io/packagist/v/marcino0o/openai.svg?style=flat-square)](https://packagist.org/packages/marcino0o/openai)
[![Packagist Downloads](https://img.shields.io/packagist/dt/marcino0o/openai.svg?style=flat-square)](https://packagist.org/packages/marcino0o/openai)
[![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/marcino0o/openai/release.yml?branch=main&label=release&style=flat-square)](https://github.com/marcino0o/openai/actions)
[![License](https://img.shields.io/github/license/marcino0o/openai.svg?style=flat-square)](LICENSE)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.4-blue?style=flat-square)](https://www.php.net/)
[![Tests](https://img.shields.io/github/actions/workflow/status/marcino0o/openai/tests.yml?label=tests&style=flat-square)](https://github.com/marcino0o/openai/actions/workflows/tests.yml)

---
## Table of Contents

* [Requirements](#requirements)
* [Installation](#installation)
* [Quick Start](#quick-start)
* [Configuration](#configuration)
* [Usage](#usage)

    * [Chat Completions – Basics](#chat-completions--basics)
    * [System Role / Messages](#system-role--messages)
    * [Model Selection](#model-selection)
    * [Images](#images)
    * [Audio](#audio)
    * [Embeddings](#embeddings)
    * [Moderations](#moderations)
* [Supported Endpoints](#supported-endpoints)
* [Error Handling](#error-handling)
* [Retry / Timeout / Streaming](#retry--timeout--streaming)
* [Logging & Telemetry](#logging--telemetry)
* [Contributing](#contributing)
* [Versioning](#versioning)
* [License](#license)

---

## Requirements

* PHP 8.4+
* Composer

---

## Installation

### From Packagist

```bash
composer require marcino0o/openai
```

---

## Quick Start

```bash
export OPENAI_API_KEY="sk-..."
```

```php
<?php
require 'vendor/autoload.php';

use OpenAI\OpenAISDK;
use OpenAI\OpenAIHTTPClient;
use OpenAI\Messages;
use OpenAI\Message;

$sdk = new OpenAISDK(
    new OpenAIHTTPClient(apiKey: getenv('OPENAI_API_KEY'))
);

$response = $sdk->createChatCompletion(
    new Messages(
        Message::fromUser('Tell me a funny programming joke!')
    )
);

echo $response->content();
```

---

## Configuration

* **API key**: via constructor or `OPENAI_API_KEY` env var.
* **Default model**: configurable via `OPENAI_MODEL` or passed per request.
* **Timeouts/Retry**: basic support (advanced retry middleware planned).

---

## Usage

### Chat Completions – Basics

```php
$response = $sdk->createChatCompletion(
    new Messages(
        Message::fromUser('Explain recursion in a short PHP example.')
    )
);
echo $response->choices[0]->message->content;
```

**Example response (truncated):**

```json
{
  "id": "chatcmpl-abc123",
  "object": "chat.completion",
  "created": 1728561000,
  "model": "gpt-4o-mini",
  "choices": [
    {
      "index": 0,
      "message": {
        "role": "assistant",
        "content": "In PHP, a recursive function calls itself..."
      },
      "finish_reason": "stop"
    }
  ],
  "usage": {
    "prompt_tokens": 28,
    "completion_tokens": 55,
    "total_tokens": 83
  }
}
```

```php
$response = $sdk->createChatCompletion(
    new Messages(
        Message::fromUser('Explain recursion in a short PHP example.')
    )
);
echo $response->choices[0]->message->content;
```

### System Role / Messages

```php
$response = $sdk->createChatCompletion(
    new Messages(
        Message::fromSystem('You are a concise assistant replying in English.'),
        Message::fromUser('Summarize monads in 2 sentences.')
    )
);
```

**Example response (truncated):**

```json
{
  "choices": [
    {
      "message": {
        "role": "assistant",
        "content": "Monads are a design pattern from category theory..."
      }
    }
  ]
}
```

### Model Selection

```php
$response = $sdk->createChatCompletion(
    new Messages(
        Message::fromUser('Summarize this in one paragraph: ...')
    ),
    model: getenv('OPENAI_MODEL') ?: 'gpt-4o-mini'
);
```

**Example response metadata:**

```json
{
  "model": "gpt-4o-mini",
  "system_fingerprint": "fp_3a1bc...",
  "usage": {"prompt_tokens": 120, "completion_tokens": 72, "total_tokens": 192}
}
```

### Images

```php
$response = $sdk->createImage(prompt: 'A futuristic city skyline at sunset');
file_put_contents('city.png', base64_decode($response->data[0]->b64_json));
```

**Example response (truncated):**

```json
{
  "created": 1728561000,
  "data": [
    {
      "b64_json": "iVBORw0KGgoAAAANSUhEUgAA..."
    }
  ]
}
```

### Audio

```php
$response = $sdk->transcribeAudio(file: 'speech.mp3', model: 'gpt-4o-mini-transcribe');
echo $response->text;
```

**Example response:**

```json
{
  "task": "transcribe",
  "language": "en",
  "duration": 12.81,
  "text": "Hello everyone, welcome to...",
  "segments": [
    {"id": 0, "start": 0.0, "end": 3.1, "text": "Hello everyone,"}
  ]
}
```

### Embeddings

```php
$response = $sdk->createEmbedding(input: 'The quick brown fox jumps over the lazy dog.');
print_r($response->data[0]->embedding);
```

**Example response (truncated):**

```json
{
  "object": "list",
  "data": [
    {
      "object": "embedding",
      "index": 0,
      "embedding": [0.0123, -0.0456, 0.0789, ...]
    }
  ],
  "model": "text-embedding-3-small",
  "usage": {"prompt_tokens": 9, "total_tokens": 9}
}
```

### Moderations

```php
$response = $sdk->createModeration(input: 'This text contains hate speech.');
if ($response->results[0]->flagged) {
    echo 'Content flagged for moderation';
}
```

**Example response:**

```json
{
  "id": "modr-abc123",
  "model": "omni-moderation-latest",
  "results": [
    {
      "flagged": true,
      "categories": {
        "hate": true,
        "self-harm": false,
        "violence": false
      },
      "category_scores": {
        "hate": 0.92,
        "self-harm": 0.01,
        "violence": 0.07
      }
    }
  ]
}
```

---

## Supported Endpoints

| Endpoint                       | Status          | Notes                  |
| ------------------------------ | --------------- | ---------------------- |
| Chat Completions               | ✅ Stable        | Primary focus          |
| Images (generation/edit)       | ⚠️ Experimental | API subject to change  |
| Audio (STT/TTS)                | ⚠️ Experimental | Partial support        |
| Embeddings                     | ⚙️ Beta         | Core logic implemented |
| Moderations                    | ⚙️ Beta         | Basic support present  |
| Files / Fine-tuning            | ⏳ Planned       | Not yet implemented    |
| Assistants / Realtime / Agents | ⏳ Planned       | Upcoming               |

---

## Error Handling

Wrap calls in `try/catch` blocks:

```php
try {
    $response = $sdk->createChatCompletion(new Messages(Message::fromUser('Ping')));
} catch (Exception $e) {
    error_log($e->getMessage());
}
```

---

## Retry / Timeout / Streaming

* **Timeouts** – configurable via HTTP client.
* **Retry** – exponential backoff (planned in upcoming release).
* **Streaming** – future `stream()` API for live token events.

---

## Logging & Telemetry

* Optional PSR-3 logger.
* Mask sensitive info (e.g., API keys, full prompts).

---

## Contributing

Contributions welcome!

1. Documentation, examples, FAQ.
2. Tests (SSE parsing, error mapping, retries).
3. Embeddings/Moderations refinements.
4. Retry middleware with exponential backoff.

Workflow:

* Fork → feature branch → PR with examples.
* CI (GitHub Actions) runs PHPUnit on PHP 8.4.

---

## Versioning

* Semantic Versioning (**SemVer**).
* Breaking changes possible until `v1.0.0`.
* See `CHANGELOG.md` after first release.

---

## License

[MIT](LICENSE)

---

## FAQ

**Does it support streaming?**
Streaming API planned (`stream()` returning partial deltas).

**Which models can I use?**
Any available in your OpenAI account (e.g., `gpt-4o-mini`). Keep model names in ENV variables.

**Is it production-ready?**
Yes for small/medium workloads. For heavy production, enable retry logic, monitoring, and pin a stable release.
