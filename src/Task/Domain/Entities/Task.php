<?php
declare(strict_types=1);

namespace App\Task\Domain\Entities;

use App\Task\Domain\VO\Description;
use App\Task\Domain\VO\TaskId;
use App\Task\Domain\VO\Priority;
use App\Task\Domain\VO\Status;
use App\Task\Domain\VO\Title;
use App\Task\Domain\VO\OwnerId;
use App\Task\Infrastructure\DB\DbTaskRepository;
use App\Task\Infrastructure\DB\DoctrineTypes\DescriptionType;
use App\Task\Infrastructure\DB\DoctrineTypes\OwnerIdType;
use App\Task\Infrastructure\DB\DoctrineTypes\TaskIdType;
use App\Task\Infrastructure\DB\DoctrineTypes\TitleType;
use DateTimeImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;

#[Entity(repositoryClass: DbTaskRepository::class)]
#[Table(name: 'tasks')]
#[Index(fields: ["title", "description"], name: "search", flags: ["fulltext"])]
class Task
{
    public function __construct(
        #[Id, Column(type: TaskIdType::NAME, length: 4, options: ['fixed' => true])]
        public readonly TaskId     $id,
        #[Column(type: OwnerIdType::NAME, length: 8, options: ['fixed' => true])]
        public readonly OwnerId    $ownerId,
        #[Column(type: TitleType::NAME, length: 100)]
        private Title              $title,
        #[Column(type: DescriptionType::NAME, length: 65535)]
        private Description        $description,
        #[Column(enumType: "integer", options: ["check" => "priority BETWEEN 1 AND 5"])]
        private Priority           $priority,
        #[Column(length: 10, enumType: "string")]
        private Status             $status = Status::TODO,
        #[Column(type: "datetime_immutable", options: ["default" => "CURRENT_TIMESTAMP"])]
        private DateTimeImmutable  $createdAt = new DateTimeImmutable(),
        #[Column(type: "datetime_immutable", nullable: true)]
        private ?DateTimeImmutable $completedAt = null,
        #[Column(type: TaskIdType::NAME, length: 4, nullable: true, options: ['fixed' => true])]
        private ?TaskId            $epicTaskId = null,
    ) {
    }

    public function changeEpicTask(?TaskId $newEpicTaskId): void
    {
        $this->epicTaskId = $newEpicTaskId;
    }

    public function changePriority(Priority $newPriority): void
    {
        $this->priority = $newPriority;
    }

    public function changeStatus(Status $newStatus): void
    {
        if ($newStatus === Status::DONE) {
            $this->status = Status::DONE;
            $this->completedAt = new DateTimeImmutable();
            return;
        }
        $this->status = $newStatus;
        $this->completedAt = null;
    }

    public function editDescription(Description $newDescription): void
    {
        $this->description = $newDescription;
    }

    public function editTitle(Title $newTitle): void
    {
        $this->title = $newTitle;
    }

    public function getCompletedAt(): ?DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getDescription(): Description
    {
        return $this->description;
    }

    public function getEpicTaskId(): ?TaskId
    {
        return $this->epicTaskId;
    }

    public function getPriority(): Priority
    {
        return $this->priority;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function getTitle(): Title
    {
        return $this->title;
    }
}