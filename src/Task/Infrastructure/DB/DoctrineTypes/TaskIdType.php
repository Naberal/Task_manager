<?php
declare(strict_types=1);

namespace App\Task\Infrastructure\DB\DoctrineTypes;

use App\Task\Domain\VO\TaskId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;
use InvalidArgumentException;

class TaskIdType extends StringType
{
    public const string NAME = 'task_id';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value instanceof TaskId ? $value->id : $value;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?TaskId
    {
        return $value !== null ? new TaskId($value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}