<?php

namespace App\Infrastructure\DB\DoctrineTypes;

use App\Domain\VO\TaskId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class TaskIdType extends StringType
{
    public const string NAME = 'task_id';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value instanceof TaskId ? $value->id : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value !== null ? new TaskId($value) : null;
    }

    public function getName()
    {
        return self::NAME;
    }
}
