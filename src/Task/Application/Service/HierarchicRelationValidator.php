<?php
declare(strict_types=1);

namespace App\Task\Application\Service;

use App\Task\Domain\Entities\Task;
use App\Task\Domain\Service\TaskRepository;
use App\Task\Domain\VO\Status;
use App\Task\Domain\VO\TaskId;
use DomainException;

/**
 * WARNING: manual injection
 */
class HierarchicRelationValidator implements RelationValidator
{
    public function __construct(private readonly TaskRepository $repository)
    {
    }

    /**
     * Validate if an epic task and subtask or potential subtask do not have self-reference or circular dependency.
     *
     * @param Task|null $epicTask
     * @param Task ...$subtasks
     * @return void
     * @throws DomainException
     */
    public function validateRelation(?Task $epicTask, Task ...$subtasks): void
    {
        if ($epicTask === null) {
            return;
        }
        if ($this->isRelationCombined($epicTask, ...$subtasks)) {
            return;
        }

        throw new DomainException('Unacceptable relationship');
    }

    private function isRelationCombined(Task $epicTask, ...$subtasks): bool
    {
        foreach ($subtasks as $subtask) {
            if ($epicTask->id->id === $subtask->id->id || in_array($epicTask->id, $this->getAllLowerSubTasksId($subtask->id))) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param TaskId $id
     * @return TaskId[]
     */
    private function getAllLowerSubTasksId(TaskId $id): array
    {
        $taskIds = array_map(fn(Task $t) => $t->id, $this->repository->getSubTasks($id));

        if (empty($taskIds)) {
            return [];
        }

        $allSubtaskIds = $taskIds;
        $lowerLevelIds = [];

        foreach ($taskIds as $taskId) {
            $lowerLevelIds[] = $this->getAllLowerSubTasksId($taskId);
        }

        if (!empty($lowerLevelIds)) {
            $allSubtaskIds = array_merge($allSubtaskIds, ...$lowerLevelIds);
        }

        return $allSubtaskIds;
    }
}