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
     * @param array $criteria
     * @param array|null $orderBy
     * @return Task[]
     */
    public function findBy(array $criteria, ?array $orderBy = null): array;

    public function remove(TaskId $id): void;

    public function update(Task $task): void;
}
