<?php

declare (strict_types=1);

namespace Gtd\Symfony\Shared\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class ApiTokenUserProvider implements UserProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername(string $username)
    {
        return new ApiUser($username);
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass(string $class)
    {
        return ApiUser::class === $class;
    }
}
