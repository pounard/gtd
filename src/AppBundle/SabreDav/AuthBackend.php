<?php

namespace AppBundle\SabreDav;

use Sabre\DAV\Auth\Backend\BackendInterface;
use Sabre\HTTP\RequestInterface;
use Sabre\HTTP\ResponseInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AuthBackend implements BackendInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * Constructor
     *
     * @param SecurityContextInterface $context
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * {@inheritdoc}
     */
    public function check(RequestInterface $request, ResponseInterface $response)
    {
        $user = $this->tokenStorage->getToken();

        // We do have nothing to check, Symfony security firewall will do
        // it for us, if correctly configured.
        if (!$user) {
            return [false, "no user"];
        }

        return [true, 'principals/users/' . $user->getUsername()];
    }

    /**
     * {@inheritdoc}
     */
    public function challenge(RequestInterface $request, ResponseInterface $response)
    {
    }
}
