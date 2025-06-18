<?php
declare(strict_types=1);

namespace App\Task\Application\Service;

use App\Task\Domain\Entities\Task;

/**
 * WARNING: manual injection
 */
interface RelationValidator
{
    /**
     * Validate if an epic task and subtask or potential subtask can have healthy relationships.
     */
    public function validateRelation(?Task $epicTask, Task ...$subtasks): void;
}