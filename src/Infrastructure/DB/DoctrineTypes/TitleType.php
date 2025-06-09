<?php

namespace App\Infrastructure\DB\DoctrineTypes;

use App\Domain\VO\Title;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class TitleType extends StringType
{
    public const string NAME = 'title';
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value instanceof Title ? $value->title : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value !== null ? new Title($value) : null;
    }

    public function getName()
    {
        return self::NAME;
    }
}
