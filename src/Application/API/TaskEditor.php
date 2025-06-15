<?php

namespace App\Application\API;

use App\Domain\VO\TaskId;

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
