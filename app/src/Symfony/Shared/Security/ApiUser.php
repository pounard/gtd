<?php

declare (strict_types=1);

namespace Gtd\Symfony\Shared\Security;

use Symfony\Component\Security\Core\User\UserInterface;

final class ApiUser implements UserInterface
{
    private string $username;

    public function __construct(string $username)
    {
        $this->username = $username;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
    }
}
