<?php

namespace App\Application\API;

use App\Application\DTO\Sort;
use App\Application\DTO\TaskFilters;
use App\Domain\Entities\Task;
use App\Domain\VO\TaskId;

interface TaskLoader
{
    /**
     * @param string|null $searchTerm
     * @param TaskFilters $filterBy
     * @param Sort $orderBy
     * @return Task[]
     */
    public function loadBy(?string $searchTerm, TaskFilters $filterBy, Sort $orderBy): array;

    public function loadById(TaskId $id): ?Task;
}
