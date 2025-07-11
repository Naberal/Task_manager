<?php
declare(strict_types=1);

namespace App\Task\Application\API;

use App\Task\Application\DTO\Sort;
use App\Task\Application\DTO\TaskFilters;
use App\Task\Domain\Entities\Task;
use App\Task\Domain\VO\OwnerId;
use App\Task\Domain\VO\TaskId;

interface TaskLoader
{
    /**
     * @param OwnerId $ownerId
     * @param string|null $searchTerm
     * @param TaskFilters $filterBy
     * @param Sort $orderBy
     * @return Task[]
     */
    public function loadBy(OwnerId $ownerId, ?string $searchTerm, TaskFilters $filterBy, Sort $orderBy): array;

    public function loadById(TaskId $id): ?Task;
}