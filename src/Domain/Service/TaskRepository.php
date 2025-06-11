<?php

namespace App\Domain\Service;

use App\Domain\Entities\Task;
use App\Domain\VO\Description;
use App\Domain\VO\TaskId;
use App\Domain\VO\Priority;
use App\Domain\VO\Status;
use App\Domain\VO\Title;

interface TaskRepository
{
    public function create(Task $task): void;

    public function getById(TaskId $id): ?Task;

    /**
     * @param TaskId $id
     * @return Task[]
     */
    public function getSubTasks(TaskId $id): array;

    /**
     * @param string|null $searchTerm
     * @param Status[] $statuses
     * @param Priority[] $priorities
     * @param array $sortBy
     * @return Task[]
     */
    public function loadBy(
        ?string $searchTerm = null,
        array   $statuses = [],
        array   $priorities = [],
        array   $sortBy = []
    ): array;

    public function remove(TaskId $id): void;

    public function update(Task $task): void;
}
