<?php
declare(strict_types=1);

namespace App\Task\Domain\VO;

use App\Lib\IdGenerator;
use InvalidArgumentException;

readonly class OwnerId
{
    /**
     * @param string $id
     * @throws InvalidArgumentException
     */
    public function __construct(public string $id)
    {
        $trimmedId = trim($id);
        if (strlen($trimmedId) !== strlen($this->id)) {
            throw new InvalidArgumentException('Owner ID contains whitespace');
        }
        if (strlen($trimmedId) !== 8) {
            throw new InvalidArgumentException('Owner ID must be 8 characters long');
        }
    }

    public function __toString(): string
    {
        return $this->id;
    }
}