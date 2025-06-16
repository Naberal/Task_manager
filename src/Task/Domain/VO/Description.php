<?php
declare(strict_types=1);

namespace App\Task\Domain\VO;

use InvalidArgumentException;

class Description
{
    /**
     * @param string $description
     * @throws InvalidArgumentException
     */
    public function __construct(public string $description)
    {
        if (empty($description)) {
            throw new InvalidArgumentException('Description cannot be empty');
        }
    }

    public function __toString(): string
    {
        return $this->description;
    }
}