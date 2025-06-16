<?php
declare(strict_types=1);

namespace App\User\Domain\Entities;

use App\User\Domain\VO\ApiKey;
use App\User\Domain\VO\UserId;
use App\User\Infrastructure\DB\DoctrineType\ApiKeyType;
use App\User\Infrastructure\DB\DoctrineType\UserIdType;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Symfony\Component\Security\Core\User\UserInterface;

#[Entity]
#[Table(name: 'users')]
class User implements UserInterface
{
    public function __construct(
        #[Id, Column(type: UserIdType::NAME, length: 8, options: ['fixed' => true])]
        public readonly UserId $id,
        #[Column(type: ApiKeyType::NAME, length: 60, unique: true, options: ['fixed' => true])]
        private ApiKey         $apiKey,
    ) {
    }

    public function eraseCredentials(): void
    {
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function getUserIdentifier(): string
    {
        return $this->id->id;
    }

    public function getApiKey(): string
    {
        return (string) $this->apiKey;
    }
}