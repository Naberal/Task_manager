<?php
declare(strict_types=1);

namespace App\Task\Application\Service;

use App\Task\Domain\Entities\Task;
use App\Task\Domain\Service\TaskRepository;
use App\Task\Domain\VO\Status;
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
     * Validate if an epic task and subtask or potential subtask do not have hierarchic conflicts
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
            if ($epicTask->id->id === $subtask->id->id || $epicTask->getEpicTaskId()?->id === $subtask->id->id) {
                return false;
            }
        }
        return true;
    }
}