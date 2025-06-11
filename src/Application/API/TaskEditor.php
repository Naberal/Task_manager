<?php

namespace App\Application\API;

use App\Domain\VO\Description;
use App\Domain\VO\TaskId;
use App\Domain\VO\Priority;
use App\Domain\VO\Status;
use App\Domain\VO\Title;

interface TaskEditor
{
    public function changeEpicTask(TaskId $id, ?TaskId $newEpicTaskId): void;

    public function changePriority(TaskId $id, Priority $newPriority): void;

    public function changeStatus(TaskId $id, Status $newStatus): void;

    public function editDescription(TaskId $id, Description $newDescription): void;

    public function editTitle(TaskId $id, Title $newTitle): void;
}
