# openai

PHP package for easy communication with openai api

## Requirements

* PHP >= 8.2
* composer

## Installation

Using composer:

```console
composer require "marcino0o/openai"
```

## Usage

### Create chat completion

#### The simplest api call 

```php
<?php

require('vendor/autoload.php');

use Openai\OpenAIHTTPClient;
use Openai\OpenAISDK;

$sdk = new OpenAISDK(new OpenAIHTTPClient(apiKey: 'openai_api_key'));
$chatCompletion = $sdk->createChatCompletion(
    new Messages(
        Message::fromUser('Hello'),
    )
);
```

#### With additional hints for system
```php
<?php

require('vendor/autoload.php');

use Openai\OpenAIHTTPClient;
use Openai\OpenAISDK;

$sdk = new OpenAISDK(new OpenAIHTTPClient(apiKey: 'openai_api_key'));
$sdk->createChatCompletion(
    new Messages(
        Message::fromSystem('You are william William Shakespeare. Your answers should go as poems.'),
        Message::fromUser('Hello'),
    )
);
```

#### Using different model

By default, chat completions uses GPT3.5 turbo model. To use other one use model parameter with Model enum.

```php
<?php

require('vendor/autoload.php');

use Openai\Model;
use Openai\OpenAIHTTPClient;
use Openai\OpenAISDK;

$sdk = new OpenAISDK(new OpenAIHTTPClient(apiKey: 'openai_api_key'));
$sdk->createChatCompletion(
    new Messages(Message::fromUser('Hello')),
    model: Model::GPT4
);
```
