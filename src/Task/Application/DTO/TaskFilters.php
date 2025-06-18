<?php
declare(strict_types=1);

namespace App\Task\Application\DTO;

use App\Task\Domain\VO\Priority;
use App\Task\Domain\VO\Status;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'TaskFilters',
    description: 'Filters for tasks',
    type: 'object'
)]
readonly class TaskFilters
{
    public function __construct(
        #[OA\Property(
            property: 'statuses',
            description: 'Filter tasks by status',
            type: 'array',
            items: new OA\Items(type: 'string', enum: ['todo', 'done'])
        )]
        public array $statuses = [],
        #[OA\Property(
            property: 'priorities',
            description: 'Filter tasks by priority',
            type: 'array',
            items: new OA\Items(type: 'integer', enum: [1, 2, 3, 4, 5])
        )]
        public array $priorities = []
    ) {
    }

    /**
     * @return Priority[]
     */
    public function getPriorities(): array
    {
        $priorities = [];
        foreach ($this->priorities as $priority) {
            $priorities[] = Priority::from((int)$priority);
        }
        return $priorities;
    }

    /**
     * @return Status[]
     */
    public function getStatuses(): array
    {
        $statuses = [];
        foreach ($this->statuses as $status) {
            $statuses[] = Status::from($status);
        }
        return $statuses;
    }
}