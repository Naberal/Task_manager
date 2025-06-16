<?php
declare(strict_types=1);

namespace App\Task\Application\API;

use App\Task\Domain\VO\TaskId;

interface TaskEditor
{
    /**
     * Updates a task with the provided data
     *
     * @param TaskId $id
     * @param array<string,string|int> $data Associative array of fields to update
     * @return void
     */
    public function edit(TaskId $id, array $data): void;
}