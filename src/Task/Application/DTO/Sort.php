<?php
declare(strict_types=1);

namespace App\Task\Application\DTO;


use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Sort',
    description: 'Sorting parameters for tasks',
    type: 'object'
)]
readonly class Sort
{
    private const array ALLOWED_FIELDS = ['priority', 'createdAt', 'completedAt'];


    public function __construct(
        #[OA\Property(
            property: 'sort',
            description: 'Sort tasks by : priority, createdAt, completedAt. Use :asc or :desc to sort in ascending or descending order.',
            type: 'array',
            items: new OA\Items(type: 'string', example: 'priority:asc'),
            example: ['priority:asc', 'createdAt:desc']
        )]
        private array $sort = []
    ) {
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