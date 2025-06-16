<?php
declare(strict_types=1);

namespace App\User\Infrastructure\DB\DoctrineType;

use App\User\Domain\VO\UserId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;
use InvalidArgumentException;

class UserIdType extends StringType
{
    public const string NAME = 'user_id';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value instanceof UserId ? $value->id : $value;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?UserId
    {
        return $value !== null ? new UserId($value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}