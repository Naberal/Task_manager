<?php

namespace App\Domain\VO;

use App\Lib\IdGenerator;
use InvalidArgumentException;

readonly class TaskId
{
    public function __construct(public string $id)
    {
        $trimmedId = trim($id);
        if (strlen($trimmedId) !== strlen($this->id)) {
            throw new InvalidArgumentException('Task ID contains whitespace');
        }
        if (strlen($trimmedId) !== 4) {
            throw new InvalidArgumentException('Task ID must be 4 characters long');
        }
    }

    public function __toString(): string
    {
        return $this->id;
    }

    public static function generate(): self
    {
        return new self(IdGenerator::generate());
    }
}
