<?php

namespace App\Application\API;

use App\Application\DTO\Sort;
use App\Application\DTO\TaskFilters;
use App\Domain\Entities\Task;
use App\Domain\Service\TaskRepository;
use App\Domain\VO\TaskId;

class TaskLoaderImpl implements TaskLoader
{
    public function __construct(private readonly TaskRepository $repository)
    {
    }

    /**
     * @inheritDoc
     */
    public function loadBy(?string $searchTerm, TaskFilters $filterBy, Sort $orderBy): array
    {
        return $this->repository->loadBy(
            $searchTerm,
            $filterBy->getStatuses(),
            $filterBy->getPriorities(),
            $orderBy->getSort()
        );
    }

    public function loadById(TaskId $id): ?Task
    {
        return $this->repository->getById($id);
    }
}
