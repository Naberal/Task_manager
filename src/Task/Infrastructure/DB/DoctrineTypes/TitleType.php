<?php
declare(strict_types=1);

namespace App\Task\Infrastructure\DB\DoctrineTypes;

use App\Task\Domain\VO\Title;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;
use InvalidArgumentException;

class TitleType extends StringType
{
    public const string NAME = 'title';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value instanceof Title ? $value->title : $value;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?Title
    {
        return $value !== null ? new Title($value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}