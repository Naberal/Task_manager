<?php
declare(strict_types=1);

namespace App\Task\Domain\Service;

use App\Task\Domain\Entities\Task;
use App\Task\Domain\VO\OwnerId;
use App\Task\Domain\VO\TaskId;
use App\Task\Domain\VO\Priority;
use App\Task\Domain\VO\Status;

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
     * @param OwnerId $ownerId
     * @param string|null $searchTerm
     * @param Status[] $statuses
     * @param Priority[] $priorities
     * @param array $sortBy
     * @return Task[]
     */
    public function loadBy(
        OwnerId $ownerId,
        ?string $searchTerm = null,
        array   $statuses = [],
        array   $priorities = [],
        array   $sortBy = []
    ): array;

    public function remove(TaskId $id): void;

    public function update(Task $task): void;
}