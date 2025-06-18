<?php
declare(strict_types=1);

namespace App\Task\Application\Service;

use App\Task\Domain\Entities\Task;
use App\Task\Domain\VO\Status;
use DomainException;

/**
 * WARNING: manual injection
 */
class StatusRelationValidator implements RelationValidator
{
    public function __construct(private readonly RelationValidator $relationValidator)
    {
    }

    /**
     * Validate if an epic task and subtask or potential subtask do not have statuses conflicts
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
        if ($this->isStatusesCombined($epicTask, ...$subtasks)) {
            $this->relationValidator->validateRelation($epicTask, ...$subtasks);
            return;
        }

        throw new DomainException('Unacceptable relationship');
    }

    private function isStatusDone(Task $task): bool
    {
        return $task->getStatus() === Status::DONE;
    }

    private function isStatusesCombined(Task $epicTask, Task ...$subtasks): bool
    {
        $isParentDone = $this->isStatusDone($epicTask);
        $isChildDone = true;
        foreach ($subtasks as $subtask) {
            if (!$this->isStatusDone($subtask)) {
                $isChildDone = false;
            }
        }
        return ($isChildDone && $isParentDone)
            || ($isChildDone && !$isParentDone)
            || (!$isChildDone && !$isParentDone);
    }
}