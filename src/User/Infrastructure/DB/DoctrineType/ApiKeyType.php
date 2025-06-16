<?php
declare(strict_types=1);

namespace App\User\Infrastructure\DB\DoctrineType;

use App\Task\Domain\VO\OwnerId;
use App\Task\Domain\VO\TaskId;
use App\User\Domain\VO\ApiKey;
use App\User\Domain\VO\UserId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;
use InvalidArgumentException;

class ApiKeyType extends StringType
{
    public const string NAME = 'apikey';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value instanceof ApiKey ? $value->apikey : $value;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?ApiKey
    {
        return $value !== null ? new ApiKey($value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}