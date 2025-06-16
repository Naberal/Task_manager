<?php
declare(strict_types=1);

namespace App\Task\Domain\VO;

use App\Lib\IdGenerator;
use InvalidArgumentException;

readonly class TaskId
{
    /**
     * @param string $id
     * @throws InvalidArgumentException
     */
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

    /**
     * @return self
     * @throws InvalidArgumentException
     */
    public static function generate(): self
    {
        return new self(IdGenerator::generate());
    }
}