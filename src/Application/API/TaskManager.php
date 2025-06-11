<?php
declare(strict_types=1);

namespace App\Application\API;

use App\Application\API\Service\RelationValidator;
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

    public function __construct(
        private readonly TaskRepository    $repository,
        private readonly RelationValidator $relationValidator
    ) {
    }

    /**
     * @param TaskId $id
     * @param TaskId|null $newEpicTaskId
     * @return void
     * @throws InvalidArgumentException
     */
    public function changeEpicTask(TaskId $id, ?TaskId $newEpicTaskId): void
    {
        $task = $this->getTask($id);
        if ($newEpicTaskId === $task->getEpicTaskId()) {
            return;
        }
        $epicTask = $this->getEpicTask($newEpicTaskId);
        $this->relationValidator->validateRelation($epicTask, $task);
        $task->changeEpicTask($newEpicTaskId);
        $this->repository->update($task);
    }

    /**
     * @param TaskId $id
     * @param Priority $newPriority
     * @return void
     * @throws InvalidArgumentException
     */
    public function changePriority(TaskId $id, Priority $newPriority): void
    {
        $task = $this->getTask($id);
        if ($newPriority === $task->getPriority()) {
            return;
        }
        $task->changePriority($newPriority);
        $this->repository->update($task);
    }

    /**
     * @param TaskId $id
     * @param Status $newStatus
     * @return void
     * @throws InvalidArgumentException
     */
    public function changeStatus(TaskId $id, Status $newStatus): void
    {
        $task = $this->getTask($id);
        if ($newStatus === $task->getStatus()) {
            return;
        }
        $task->changeStatus($newStatus);
        $epicTask = $this->getEpicTask($task->getEpicTaskId());
        $this->relationValidator->validateRelation($epicTask, $task);
        $this->relationValidator->validateRelation($task, ...$this->repository->getSubTasks($id));
        $this->repository->update($task);
    }

    /**
     * @param TaskId $id
     * @param Description $newDescription
     * @return void
     * @throws InvalidArgumentException
     */
    public function editDescription(TaskId $id, Description $newDescription): void
    {
        $task = $this->getTask($id);
        if ($newDescription === $task->getDescription()) {
            return;
        }
        $task->editDescription($newDescription);
        $this->repository->update($task);
    }

    /**
     * @param TaskId $id
     * @param Title $newTitle
     * @return void
     * @throws InvalidArgumentException
     */
    public function editTitle(TaskId $id, Title $newTitle): void
    {
        $task = $this->getTask($id);
        if ($newTitle === $task->getTitle()) {
            return;
        }
        $task->editTitle($newTitle);
        $this->repository->update($task);
    }

    /**
     * @param Task $task
     * @return void
     * @throws InvalidArgumentException
     */
    public function create(Task $task): void
    {
        $epicTask = $this->getEpicTask($task->getEpicTaskId());
        $this->relationValidator->validateRelation($epicTask, $task);
        $this->repository->create($task);
    }

    /**
     * @param TaskId $id
     * @return void
     * @throws InvalidArgumentException|DomainException
     */
    public function remove(TaskId $id): void
    {
        $task = $this->getTask($id);
        if ($task->getStatus() === Status::DONE) {
            throw new DomainException('Cannot remove done task');
        }
        $this->repository->remove($id);
    }

    /**
     * @param TaskId|null $epicTaskId
     * @return Task|null
     * @throws InvalidArgumentException
     */
    private function getEpicTask(?TaskId $epicTaskId): ?Task
    {
        if ($epicTaskId === null) {
            return null;
        }
        $epicTask = $this->repository->getById($epicTaskId);
        if ($epicTask === null) {
            throw new InvalidArgumentException('Epic task does not exist');
        }
        return $epicTask;
    }

    /**
     * @param TaskId $id
     * @return Task
     * @throws InvalidArgumentException
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
