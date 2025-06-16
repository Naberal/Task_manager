<?php
declare(strict_types=1);

namespace App\Task\Application\Service;

use App\Task\Domain\Entities\Task;

interface AccessAuthorizer
{
    public function validate(Task $task): void;
}