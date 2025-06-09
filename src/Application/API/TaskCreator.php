<?php

namespace App\Application\API;

use App\Domain\Entities\Task;

interface TaskCreator
{
    public function create(Task $task): void;
}
