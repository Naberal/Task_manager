<?php

namespace App\Domain\VO;

readonly class Id
{
    public function __construct(public string $id)
    {
    }
    public function __toString(): string
    {
        return $this->id;
    }
}
