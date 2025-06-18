<?php
declare(strict_types=1);

namespace App\Task\Application\Service;

use App\Task\Domain\Entities\Task;

interface AccessAuthorizer
{
    /**
     * Check if the logged-in user can interact with the task
     *
     * @param Task $task
     * @return void
     */
    public function validate(Task $task): void;
}