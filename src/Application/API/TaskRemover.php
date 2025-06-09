<?php

namespace App\Application\API;

use App\Domain\VO\TaskId;

interface TaskRemover
{
    public function remove(TaskId $id): void;
}
