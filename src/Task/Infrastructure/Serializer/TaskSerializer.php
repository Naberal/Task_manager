<?php
declare(strict_types=1);

namespace App\Task\Infrastructure\Serializer;

use App\Task\Domain\Entities\Task;
use App\Task\Domain\VO\Description;
use App\Task\Domain\VO\OwnerId;
use App\Task\Domain\VO\Priority;
use App\Task\Domain\VO\Status;
use App\Task\Domain\VO\TaskId;
use App\Task\Domain\VO\Title;
use DateMalformedStringException;
use DateTimeImmutable;
use InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class TaskSerializer implements DenormalizerInterface, NormalizerInterface
{
    /**
     * @param mixed $data
     * @param string $type
     * @param string|null $format
     * @param array $context
     * @return mixed
     * @throws InvalidArgumentException
     * @throws DateMalformedStringException
     */
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        if ($type === Task::class . '[]') {
            return array_map(fn($item) => $this->denormalizeTask($item), $data);
        }

        if ($type === Task::class) {
            return $this->denormalizeTask($data);
        }

        throw new InvalidArgumentException(sprintf('Cannot denormalize type "%s"', $type));
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === Task::class || $type === Task::class . '[]';
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Task::class => true,
            Task::class . '[]' => true,
        ];
    }

    /**
     * @param mixed $data
     * @param string|null $format
     * @param array $context
     * @return array|string|int|float|bool|\ArrayObject|null
     * @throws InvalidArgumentException
     */
    public function normalize(mixed $data, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        if (is_array($data)) {
            return array_map(fn($task) => $this->normalizeTask($task), $data);
        }

        if ($data instanceof Task) {
            return $this->normalizeTask($data);
        }

        throw new InvalidArgumentException(sprintf('Cannot normalize data of type "%s"', get_debug_type($data)));
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Task || (is_array($data) && isset($data[0]) && $data[0] instanceof Task);
    }

    /**
     * @throws DateMalformedStringException
     * @throws InvalidArgumentException
     */
    private function denormalizeTask(array $data): Task
    {
        $id = new TaskId($data['id']);
        $ownerId = new OwnerId($data['owner_id']);
        $title = new Title($data['title']);
        $description = new Description($data['description']);
        $priority = Priority::from($data['priority']);
        $status = Status::from($data['status']);
        $createdAt = new DateTimeImmutable($data['created_at']);
        $completedAt = $data['completed_at'] ? new DateTimeImmutable($data['completed_at']) : null;
        $epicTaskId = $data['epic_task_id'] ? new TaskId($data['epic_task_id']) : null;

        return new Task(
            $id,
            $ownerId,
            $title,
            $description,
            $priority,
            $status,
            $createdAt,
            $completedAt,
            $epicTaskId
        );
    }

    private function normalizeTask(Task $task): array
    {
        return [
            'id' => (string)$task->id,
            'title' => (string)$task->getTitle(),
            'description' => (string)$task->getDescription(),
            'priority' => $task->getPriority()->value,
            'status' => $task->getStatus()->value,
            'created_at' => $task->getCreatedAt()->format('Y-m-d\TH:i:s.u\Z'),
            'completed_at' => $task->getCompletedAt()?->format('Y-m-d\TH:i:s.u\Z'),
            'epic_task_id' => $task->getEpicTaskId() ? (string)$task->getEpicTaskId() : null,
        ];
    }
}