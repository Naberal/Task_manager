<?php
declare(strict_types=1);

namespace App\Task\Application\DTO;

use App\Task\Domain\VO\Priority;
use App\Task\Domain\VO\Status;

readonly class TaskFilters
{
    public function __construct(public array $status = [], public array $priority = [])
    {
    }

    /**
     * @return Priority[]
     */
    public function getPriorities(): array
    {
        $priorities = [];
        foreach ($this->priority as $priority) {
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
        foreach ($this->status as $status) {
            $statuses[] = Status::from($status);
        }
        return $statuses;
    }
}