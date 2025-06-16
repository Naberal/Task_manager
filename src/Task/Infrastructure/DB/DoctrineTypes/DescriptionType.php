<?php
declare(strict_types=1);

namespace App\Task\Infrastructure\DB\DoctrineTypes;

use App\Task\Domain\VO\Description;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\TextType;
use InvalidArgumentException;

class DescriptionType extends TextType
{
    public const string NAME = 'description';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value instanceof Description ? $value->description : $value;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?Description
    {
        return $value !== null ? new Description($value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}