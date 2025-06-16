<?php

namespace App\Task\Application\API;

use App\Task\Domain\VO\TaskId;

interface TaskRemover
{
    public function remove(TaskId $id): void;
}