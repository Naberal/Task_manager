<?php
declare(strict_types=1);

namespace App\Task\Infrastructure\DB\DoctrineTypes;

use App\Task\Domain\VO\OwnerId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;
use InvalidArgumentException;

class OwnerIdType extends StringType
{
    public const string NAME = 'owner_id';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value instanceof OwnerId ? $value->id : $value;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?OwnerId
    {
        return $value !== null ? new OwnerId($value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}