<?php
declare(strict_types=1);

namespace App\User\Domain\VO;

use App\Lib\IdGenerator;
use InvalidArgumentException;

readonly class UserId
{
    /**
     * @param string $id
     * @throws InvalidArgumentException
     */
    public function __construct(public string $id)
    {
        $trimmedId = trim($id);
        if (strlen($trimmedId) !== strlen($this->id)) {
            throw new InvalidArgumentException('User ID contains whitespace');
        }
        if (strlen($trimmedId) !== 8) {
            throw new InvalidArgumentException('User ID must be 8 characters long');
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
        return new self(IdGenerator::generate(8));
    }
}