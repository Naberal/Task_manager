<?php

namespace App\Domain\Entities;


use App\Domain\VO\Description;
use App\Domain\VO\Id;
use App\Domain\VO\Priority;
use App\Domain\VO\Status;
use App\Domain\VO\Title;
use DateTimeImmutable;

readonly class Task
{
    public function __construct(
        public Id                 $id,
        public Title              $title,
        public Description        $description,
        public Priority           $priority,
        public Status             $status = Status::TODO,
        public DateTimeImmutable  $createdAt = new DateTimeImmutable(),
        public ?DateTimeImmutable $completedAt = null,
        public ?Task              $parentId = null,
    )
    {
    }
}
