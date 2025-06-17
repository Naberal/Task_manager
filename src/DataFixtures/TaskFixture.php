<?php
declare(strict_types=1);

namespace App\DataFixtures;

use App\Task\Domain\Entities\Task;
use App\Task\Domain\VO\Description;
use App\Task\Domain\VO\OwnerId;
use App\Task\Domain\VO\Priority;
use App\Task\Domain\VO\Status;
use App\Task\Domain\VO\TaskId;
use App\Task\Domain\VO\Title;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TaskFixture extends Fixture implements DependentFixtureInterface
{
    public const TASK_1_ID = 'task';
    public const TASK_2_ID = 'tsk2';
    public const TASK_3_ID = 'tsk3';
    public const TASK_4_ID = 'tsk4';
    public const EPIC_TASK_ID = 'epic';
    public const CRITICAL_TASK_ID = 'crit';
    public const SUBTASK_1_ID = 'sub1';
    public const SUBTASK_2_ID = 'sub2';

    public function load(ObjectManager $manager): void
    {
        // Create an epic task
        $epicTask = new Task(
            new TaskId(self::EPIC_TASK_ID),
            new OwnerId(UserFixture::USER_1_ID),
            new Title('Epic Task'),
            new Description('This is an epic task that contains subtasks'),
            Priority::HIGH,
            Status::TODO,
            new DateTimeImmutable('2023-01-01 12:00:00')
        );
        $manager->persist($epicTask);
        $this->addReference('epic-task', $epicTask);

        // Create a regular task owned by user 1
        $task1 = new Task(
            new TaskId(self::TASK_1_ID),
            new OwnerId(UserFixture::USER_1_ID),
            new Title('Task 1'),
            new Description('This is the first task'),
            Priority::MEDIUM,
            Status::TODO,
            new DateTimeImmutable('2023-01-02 12:00:00'),
            null,
            new TaskId(self::EPIC_TASK_ID)
        );
        $manager->persist($task1);
        $this->addReference('task-1', $task1);

        // Create a completed task owned by user 2
        $task2 = new Task(
            new TaskId(self::TASK_2_ID),
            new OwnerId(UserFixture::USER_2_ID),
            new Title('Task 2'),
            new Description('This is the second task and it is completed'),
            Priority::LOW,
            Status::DONE,
            new DateTimeImmutable('2023-01-03 12:00:00'),
            new DateTimeImmutable('2023-01-04 12:00:00')
        );
        $manager->persist($task2);
        $this->addReference('task-2', $task2);

        // Create a critical priority task
        $criticalTask = new Task(
            new TaskId(self::CRITICAL_TASK_ID),
            new OwnerId(UserFixture::USER_1_ID),
            new Title('Critical Task'),
            new Description('This is a critical priority task that needs immediate attention'),
            Priority::CRITICAL,
            Status::TODO,
            new DateTimeImmutable('2023-01-05 12:00:00')
        );
        $manager->persist($criticalTask);
        $this->addReference('critical-task', $criticalTask);

        // Create a task owned by admin user
        $adminTask = new Task(
            new TaskId(self::TASK_3_ID),
            new OwnerId(UserFixture::USER_3_ID),
            new Title('Admin Task'),
            new Description('This task is owned by an admin user'),
            Priority::HIGH,
            Status::TODO,
            new DateTimeImmutable('2023-01-06 12:00:00')
        );
        $manager->persist($adminTask);
        $this->addReference('admin-task', $adminTask);

        // Create a task owned by guest user
        $guestTask = new Task(
            new TaskId(self::TASK_4_ID),
            new OwnerId(UserFixture::USER_4_ID),
            new Title('Guest Task'),
            new Description('This task is owned by a guest user'),
            Priority::LOW,
            Status::TODO,
            new DateTimeImmutable('2023-01-07 12:00:00')
        );
        $manager->persist($guestTask);
        $this->addReference('guest-task', $guestTask);

        // Create subtasks for the epic task
        $subtask1 = new Task(
            new TaskId(self::SUBTASK_1_ID),
            new OwnerId(UserFixture::USER_1_ID),
            new Title('Subtask 1'),
            new Description('This is the first subtask of the epic task'),
            Priority::MEDIUM,
            Status::TODO,
            new DateTimeImmutable('2023-01-08 12:00:00'),
            null,
            new TaskId(self::EPIC_TASK_ID)
        );
        $manager->persist($subtask1);
        $this->addReference('subtask-1', $subtask1);

        $subtask2 = new Task(
            new TaskId(self::SUBTASK_2_ID),
            new OwnerId(UserFixture::USER_2_ID),
            new Title('Subtask 2'),
            new Description('This is the second subtask of the epic task'),
            Priority::MINOR,
            Status::DONE,
            new DateTimeImmutable('2023-01-09 12:00:00'),
            new DateTimeImmutable('2023-01-10 12:00:00'),
            new TaskId(self::EPIC_TASK_ID)
        );
        $manager->persist($subtask2);
        $this->addReference('subtask-2', $subtask2);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixture::class,
        ];
    }
}
