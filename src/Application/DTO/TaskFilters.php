<?php

namespace App\Application\DTO;

use App\Domain\VO\Priority;
use App\Domain\VO\Status;

readonly class TaskFilters
{
    /**
     * @param Status[] $status
     * @param Priority[] $priority
     */
    public function __construct(public array $status = [], public array $priority = [])
    {
    }
}
