<?php

namespace App\Security\Authentication;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class AppAuthenticatorListener implements ListenerInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var AuthenticationManagerInterface
     */
    private $authenticationManager;

    /**
     * Uniquely identifies the secured area.
     *
     * @var string
     */
    private $providerKey;

    /**
     * @param GetResponseEvent $event
     */
    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        // extract the user credentials from the request
        $username = $request->request->get('username');
        $password = $request->request->get('password');

        // generate token for provided credentials
        $unauthenticatedToken = new UsernamePasswordToken(
            $username,
            $password,
            $this->providerKey
        );

        // ask the authentication manager to validate the given token
        $authenticatedToken = $this->authenticationManager->authenticate($unauthenticatedToken);

        // store the authenticated token
        $this->tokenStorage->setToken($authenticatedToken);
    }
}
