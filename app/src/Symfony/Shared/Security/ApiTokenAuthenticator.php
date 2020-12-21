<?php

declare (strict_types=1);

namespace Gtd\Symfony\Shared\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

final class ApiTokenAuthenticator extends AbstractAuthenticator
{
    /**
     * {@inheritdoc}
     */
    public function supports(Request $request): ?bool
    {
        // return $request->headers->has('X-AUTH-TOKEN');
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(Request $request): PassportInterface
    {
        /*
        $apiToken = $request->headers->get('X-AUTH-TOKEN');
        if (null === $apiToken) {
            // The token header was empty, authentication fails with HTTP Status
            // Code 401 "Unauthorized"
            throw new CustomUserMessageAuthenticationException('No API token provided');
        }

        return new SelfValidatingPassport(new UserBadge($apiToken));
         */

        return new SelfValidatingPassport(
            new UserBadge(
                'foo',
                fn ($username) => new ApiUser($username)
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null; // On success, let the request continue.
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(
            [
                'message' => \strtr($exception->getMessageKey(), $exception->getMessageData())
            ],
            Response::HTTP_UNAUTHORIZED
        );
    }
}
