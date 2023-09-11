<?php

declare(strict_types=1);

namespace Openai\Chat;

readonly class Message
{
    public function __construct(
        public string $content,
        public Role $role,
    ) {
    }

    public static function fromSystem(string $content): self
    {
        return new self($content, Role::SYSTEM);
    }

    public static function fromUser(string $content): self
    {
        return new self($content, Role::USER);
    }

    public static function fromAssistant(string $content): self
    {
        return new self($content, Role::ASSISTANT);
    }
}
