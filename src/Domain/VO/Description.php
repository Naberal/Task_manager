<?php

namespace App\Domain\VO;

use InvalidArgumentException;

class Description
{
    public function __construct(public string $description)
    {
        if (empty($description)) {
            throw new InvalidArgumentException('Description cannot be empty');
        }
    }
}
