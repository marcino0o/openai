<?php

declare(strict_types=1);

namespace Openai\Moderation;

readonly class ModerationResult
{
    public function __construct(
        public bool $flagged,
        public Categories $categories
    ) {
    }

    /**
     * @param array{categories: array<string, bool>, category_scores: array<string, float>, flagged: bool} $data
     */
    public static function fromArray(array $data): self
    {
        $categoriesKeys = array_keys($data['categories']);
        $categories = array_map(
            static fn (array $category): Category // @phpstan-ignore argument.type
                => new Category(
                    name: $category[0], // @phpstan-ignore argument.type
                    flagged: $category[1], // @phpstan-ignore argument.type
                    score: $category[2], // @phpstan-ignore argument.type
                ),
            array_merge_recursive(
                array_combine($categoriesKeys, $categoriesKeys),
                $data['categories'],
                $data['category_scores']
            )
        );

        return new self($data['flagged'], new Categories(...$categories));
    }
}
