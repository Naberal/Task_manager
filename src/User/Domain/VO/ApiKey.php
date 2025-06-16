<?php
declare(strict_types=1);

namespace App\User\Domain\VO;

use InvalidArgumentException;

readonly class ApiKey
{
    /**
     * @param string $apikey
     * @throws InvalidArgumentException
     */
    public function __construct(public string $apikey)
    {
        $trimmedId = trim($apikey);
        if (strlen($trimmedId) !== strlen($this->apikey)) {
            throw new InvalidArgumentException('APIkey contains whitespace');
        }
        if (strlen($trimmedId) !== 16) {
            throw new InvalidArgumentException('APIkey be 16 characters long');
        }
    }

    public function __toString(): string
    {
        return $this->apikey;
    }
}