<?php
declare(strict_types=1);

namespace App\Task\Application\API;

use App\Task\Application\DTO\Sort;
use App\Task\Application\DTO\TaskFilters;
use App\Task\Domain\Entities\Task;
use App\Task\Domain\Service\TaskRepository;
use App\Task\Domain\VO\OwnerId;
use App\Task\Domain\VO\TaskId;

class TaskLoaderImpl implements TaskLoader
{
    public function __construct(private readonly TaskRepository $repository)
    {
    }

    /**
     * @param OwnerId $ownerId
     * @param string|null $searchTerm
     * @param TaskFilters $filterBy
     * @param Sort $orderBy
     * @inheritDoc
     */
    public function loadBy(OwnerId $ownerId, ?string $searchTerm, TaskFilters $filterBy, Sort $orderBy): array
    {
        return $this->repository->loadBy(
            $ownerId,
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