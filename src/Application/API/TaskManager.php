<?php
declare(strict_types=1);

namespace App\Application\API;

use App\Domain\Entities\Task;
use App\Domain\Service\TaskRepository;
use App\Domain\VO\Description;
use App\Domain\VO\TaskId;
use App\Domain\VO\Priority;
use App\Domain\VO\Status;
use App\Domain\VO\Title;
use DomainException;
use InvalidArgumentException;

class TaskManager implements TaskCreator, TaskEditor, TaskRemover
{

    public function __construct(private readonly TaskRepository $repository)
    {
    }

    public function changeParent(TaskId $id, ?TaskId $newParentId): void
    {
        $task = $this->getTask($id);
        $this->checkParent($newParentId);
        if ($newParentId === $task->getParentId()) {
            return;
        }
        $task->changeParent($newParentId);
        $this->repository->update($task);
    }

    public function changePriority(TaskId $id, Priority $newPriority): void
    {
        $task = $this->getTask($id);
        if ($newPriority === $task->getPriority()) {
            return;
        }
        $task->changePriority($newPriority);
        $this->repository->update($task);
    }

    public function changeStatus(TaskId $id, Status $newStatus): void
    {
        $task = $this->getTask($id);
        if ($newStatus === $task->getStatus()) {
            return;
        }
        $task->changeStatus($newStatus);

        $this->repository->update($task);
    }

    public function editDescription(TaskId $id, Description $newDescription): void
    {
        $task = $this->getTask($id);
        if ($newDescription === $task->getDescription()) {
            return;
        }
        $task->editDescription($newDescription);
        $this->repository->update($task);
    }

    public function editTitle(TaskId $id, Title $newTitle): void
    {
        $task = $this->getTask($id);
        if ($newTitle === $task->getTitle()) {
            return;
        }
        $task->editTitle($newTitle);
        $this->repository->update($task);
    }

    public function create(Task $task): void
    {
        $this->checkParent($task->getParentId());
        $this->repository->create($task);
    }

    public function remove(TaskId $id): void
    {
        $task = $this->getTask($id);
        if ($task->getStatus() === Status::DONE) {
            throw new DomainException('Cannot remove done task');
        }
        $this->repository->remove($id);
    }

    /**
     * @param TaskId|null $parentId
     * @return void
     */
    private function checkParent(?TaskId $parentId): void
    {
        if ($parentId !== null && $this->repository->getById($parentId) === null) {
            throw new InvalidArgumentException('Parent task does not exist');
        }
    }

    /**
     * @param TaskId $id
     * @return Task
     */
    private function getTask(TaskId $id): Task
    {
        $task = $this->repository->getById($id);
        if ($task === null) {
            throw new InvalidArgumentException('Task does not exist');
        }
        return $task;
    }
}
