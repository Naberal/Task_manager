<?php
declare(strict_types=1);

namespace App\User\Infrastructure;

use App\User\Domain\Entities\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiKeyAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function authenticate(Request $request): Passport
    {
        $apiKey = $request->headers->get('APIKey');

        if (!$apiKey) {
            throw new CustomUserMessageAuthenticationException('API Key is missing');
        }

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['apiKey' => $apiKey]);

        if ($user === null) {
            throw new CustomUserMessageAuthenticationException('Invalid API Key');
        }

        return new SelfValidatingPassport(new UserBadge($apiKey, fn() => $user));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(['error' => 'Unauthorized'], 401);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function supports(Request $request): ?bool
    {
        $pathInfo = $request->getPathInfo();
        return str_starts_with($pathInfo, '/api/task') || str_starts_with($pathInfo, '/api/tasks');
    }
}
