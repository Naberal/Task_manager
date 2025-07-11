<?php
declare(strict_types=1);

namespace App\Task\Domain\VO;

use InvalidArgumentException;

readonly class Title
{
    /**
     * @param string $title
     * @throws InvalidArgumentException
     */
    public function __construct(public string $title)
    {
        if (empty($title)) {
            throw new InvalidArgumentException('Title cannot be empty');
        }
        if (strlen($title) >= 100) {
            throw new InvalidArgumentException('Title cannot be longer than 100 characters');
        }
    }

    public function __toString(): string
    {
        return $this->title;
    }
}