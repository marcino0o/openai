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
    * [Moderations](#moderations)
* [Supported Endpoints](#supported-endpoints)
* [Error Handling](#error-handling)
* [Retry / Timeout / Streaming](#retry--timeout--streaming)
* [Logging & Telemetry](#logging--telemetry)
* [Testing](#testing)
* [Contributing](#contributing)
* [Versioning](#versioning)
* [License](#license)
* [FAQ](#faq)

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

use Openai\OpenAISDK;
use Openai\OpenAIHTTPClient;
use Openai\Chat\Messages;
use Openai\Chat\Message;

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

echo $response->choices->current()?->message->content;
```

**Example response (truncated):**

```json
{
  "id": "chatcmpl-abc123",
  "object": "chat.completion",
  "created": 1728561000,
  "model": "gpt-4.1-mini",
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
    model: \Openai\Model::tryFromModelString(getenv('OPENAI_MODEL') ?: 'gpt-4.1-mini')
);
```

**Example response metadata:**

```json
{
  "model": "gpt-4.1-mini",
  "system_fingerprint": "fp_3a1bc...",
  "usage": {"prompt_tokens": 120, "completion_tokens": 72, "total_tokens": 192}
}
```

### Chat Completions – Advanced options

```php
use Openai\Chat\PresencePenalty;
use Openai\Chat\ReasoningEffort;
use Openai\Chat\TopP;

$response = $sdk->createChatCompletion(
    messages: new Messages(
        Message::fromSystem('You are a concise assistant.'),
        Message::fromUser('Summarize this article in 5 bullet points.')
    ),
    model: \Openai\Model::GPT_4_1,
    presencePenalty: PresencePenalty::tryFrom(0.2),
    topP: TopP::tryFrom(0.95),
    maxCompletionTokens: 400,
    stop: ['<END>'],
    reasoningEffort: ReasoningEffort::MEDIUM,
);
```

### Images

```php
$response = $sdk->createImage(prompt: \Openai\Prompt::fromString('A futuristic city skyline at sunset'));
file_put_contents('city.png', base64_decode($response->images[0]->base64));
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
$response = $sdk->createAudioTranscription(filePath: 'speech.mp3');
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

### Moderations

```php
$response = $sdk->createModeration(input: 'This text contains hate speech.');
if ($response->moderationResult->flagged) {
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
| Embeddings                     | ⏳ Planned       | Not yet implemented    |
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

## Testing

Run quality checks locally:

```bash
composer test:unit
composer test:integration
composer cs
composer stan
```

> Note: Integration tests use mocked HTTP responses and do not require a live OpenAI API request.

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
Use values from `Openai\Model`, e.g. `gpt-5`, `gpt-4.1`, `gpt-4.1-mini`, `o3`, `o4-mini`, `gpt-4o`, `gpt-4o-mini`, `omni-moderation-latest`, `gpt-4o-mini-transcribe`.

**Is it production-ready?**
Yes for small/medium workloads. For heavy production, enable retry logic, monitoring, and pin a stable release.
