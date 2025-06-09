<?php

namespace App\Infrastructure\DB\DoctrineTypes;

use App\Domain\VO\Description;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\TextType;

class DescriptionType extends TextType
{
    public const string NAME = 'description';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value instanceof Description ? $value->description : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value !== null ? new Description($value) : null;
    }

    public function getName()
    {
        return self::NAME;
    }
}
