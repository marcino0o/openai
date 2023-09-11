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

    public static function fromArray(array $data): self
    {
        $categoriesKeys = array_keys($data['categories']);
        $categories = array_map(
            static fn (array $category): Category
                => new Category(name: $category[0], flagged: $category[1], score: $category[2]),
            array_merge_recursive(
                array_combine($categoriesKeys, $categoriesKeys),
                $data['categories'],
                $data['category_scores']
            )
        );

        return new self($data['flagged'], new Categories(...$categories));
    }
}
