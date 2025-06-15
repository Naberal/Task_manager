<?php
declare(strict_types=1);

namespace App\Application\API;

use App\Application\Service\RelationValidator;
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
     * @param array $data
     * @return void
     * @throws InvalidArgumentException
     * @throws DomainException
     */
    public function edit(TaskId $id, array $data): void
    {
        $task = $this->getTask($id);
        if (isset($data['title'])) {
            $this->editTitle($task, new Title($data['title']));
        }

        if (isset($data['description'])) {
            $this->editDescription($task, new Description($data['description']));
        }

        if (isset($data['priority'])) {
            $this->changePriority($task, Priority::from($data['priority']));
        }

        if (array_key_exists('epicTaskId', $data)) {
            $epicTaskId = $data['epicTaskId'] !== null ? new TaskId($data['epicTaskId']) : null;
            $this->changeEpicTask($task, $epicTaskId);
        }

        if (isset($data['status'])) {
            $this->changeStatus($task, Status::from($data['status']));
        }
        $this->repository->update($task);
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
     * @param Task $task
     * @param TaskId|null $newEpicTaskId
     * @return void
     * @throws InvalidArgumentException
     */
    private function changeEpicTask(Task $task, ?TaskId $newEpicTaskId): void
    {

        if ($newEpicTaskId === $task->getEpicTaskId()) {
            return;
        }
        $epicTask = $this->getEpicTask($newEpicTaskId);
        $this->relationValidator->validateRelation($epicTask, $task);
        $task->changeEpicTask($newEpicTaskId);
        $this->repository->update($task);
    }

    /**
     * @param Task $task
     * @param Priority $newPriority
     * @return void
     */
    private function changePriority(Task $task, Priority $newPriority): void
    {
        if ($newPriority === $task->getPriority()) {
            return;
        }
        $task->changePriority($newPriority);
    }

    /**
     * @param Task $task
     * @param Status $newStatus
     * @return void
     * @throws InvalidArgumentException
     * @throws DomainException
     */
    private function changeStatus(Task $task, Status $newStatus): void
    {
        if ($newStatus === $task->getStatus()) {
            return;
        }
        $task->changeStatus($newStatus);
        $epicTask = $this->getEpicTask($task->getEpicTaskId());
        $this->relationValidator->validateRelation($epicTask, $task);
        $this->relationValidator->validateRelation($task, ...$this->repository->getSubTasks($task->id));
    }

    /**
     * @param Task $task
     * @param Description $newDescription
     * @return void
     */
    private function editDescription(Task $task, Description $newDescription): void
    {
        if ($newDescription === $task->getDescription()) {
            return;
        }
        $task->editDescription($newDescription);
    }

    /**
     * @param Task $task
     * @param Title $newTitle
     * @return void
     */
    private function editTitle(Task $task, Title $newTitle): void
    {
        if ($newTitle === $task->getTitle()) {
            return;
        }
        $task->editTitle($newTitle);
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
