<?php
declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\VO\Description;
use App\Domain\VO\TaskId;
use App\Domain\VO\Priority;
use App\Domain\VO\Status;
use App\Domain\VO\Title;
use App\Infrastructure\DB\DbTaskRepository;
use App\Infrastructure\DB\DoctrineTypes\DescriptionType;
use App\Infrastructure\DB\DoctrineTypes\TaskIdType;
use App\Infrastructure\DB\DoctrineTypes\TitleType;
use DateTimeImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity(repositoryClass: DbTaskRepository::class)]
#[Table(name: 'tasks')]
class Task
{
    public function __construct(
        #[Id, Column(type: TaskIdType::NAME, length: 4, options: ['fixed' => true])]
        public readonly TaskId     $id,
        #[Column(type: TitleType::NAME, length: 100)]
        private Title              $title,
        #[Column(type: DescriptionType::NAME, length: 65535)]
        private Description        $description,
        #[Column(enumType: "int")]
        private Priority           $priority,
        #[Column(enumType: "string", length: 10)]
        private Status             $status = Status::TODO,
        #[Column(type: 'datetime_immutable')]
        private DateTimeImmutable  $createdAt = new DateTimeImmutable(),
        #[Column(type: 'datetime_immutable', nullable: true)]
        private ?DateTimeImmutable $completedAt = null,
        #[Column(type: TaskIdType::NAME, length: 4, nullable: true)]
        private ?TaskId            $parentId = null,
    ) {
    }

    public function changeParent(?TaskId $newParentId): void
    {
        $this->parentId = $newParentId;
    }

    public function changePriority(Priority $newPriority): void
    {
        $this->priority = $newPriority;
    }

    public function changeStatus(Status $newStatus): void
    {
        if ($newStatus === Status::DONE) {
            $this->markAsDone();
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

    public function getParentId(): ?TaskId
    {
        return $this->parentId;
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

    public function markAsDone(): void
    {
        $this->status = Status::DONE;
        $this->completedAt = new DateTimeImmutable();
    }
}
