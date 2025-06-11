<?php
declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Entities\Task;
use App\Domain\VO\Status;
use DomainException;

class RelationValidator
{
    /**
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
        if ($this->isRelationCombined($epicTask, ...$subtasks) && $this->isStatusesCombined($epicTask, ...$subtasks)) {
            return;
        }

        throw new DomainException('Unacceptable relationship');
    }

    private function isRelationCombined(Task $epicTask, ...$subtasks): bool
    {
        foreach ($subtasks as $subtask) {
            if ($epicTask->id->id === $subtask->id->id || $epicTask->getEpicTaskId()->id === $subtask->id->id) {
                return false;
            }
        }
        return true;
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
