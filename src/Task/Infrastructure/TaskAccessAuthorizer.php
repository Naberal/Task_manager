<?php
declare(strict_types=1);

namespace App\Task\Infrastructure;

use App\Task\Application\Service\AccessAuthorizer;
use App\Task\Domain\Entities\Task;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class TaskAccessAuthorizer implements AccessAuthorizer
{
    public function __construct(private readonly TokenStorageInterface $tokenStorage)
    {
    }

    public function validate(Task $task): void
    {
        if ($this->tokenStorage->getToken()?->getUser()?->getUserIdentifier() !== $task->ownerId->id) {
            throw new AuthenticationException();
        }
    }
}