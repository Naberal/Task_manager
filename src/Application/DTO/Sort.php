<?php

namespace App\Application\DTO;

readonly class Sort
{
    private const array ALLOWED_FIELDS = ['priority', 'createdAt', 'completedAt'];

    public function __construct(private array $sort = [])
    {
    }

    public function getSort(): array
    {
        $result = [];

        foreach ($this->sort as $sortParam) {
            [$field, $direction] = explode(':', $sortParam) + [null, 'asc'];

            if (!in_array($field, self::ALLOWED_FIELDS, true)) {
                continue;
            }

            $direction = strtolower($direction);
            if (!in_array($direction, ['asc', 'desc'], true)) {
                continue;
            }

            $result[$field] = $direction;
        }

        return $result;
    }
}
