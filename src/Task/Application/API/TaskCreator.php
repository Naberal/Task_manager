<?php
declare(strict_types=1);

namespace App\Task\Application\API;

use App\Task\Domain\Entities\Task;

interface TaskCreator
{
    public function create(Task $task): void;
}