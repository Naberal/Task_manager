<?php

namespace App\Application\DTO;

use App\Domain\VO\Priority;
use App\Domain\VO\Status;

readonly class TaskFilters
{
    /**
     * @param array $status
     * @param array $priority
     */
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
            $priorities[] = Priority::from($priority);
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