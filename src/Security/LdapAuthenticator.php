<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LdapAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var CsrfTokenManagerInterface
     */
    private $csrfTokenManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * AppAuthenticator constructor.
     *
     * @param EntityManagerInterface       $entityManager
     * @param UrlGeneratorInterface        $urlGenerator
     * @param CsrfTokenManagerInterface    $csrfTokenManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function supports(Request $request)
    {
        return 'ldap_login' === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    /**
     * @param Request $request
     *
     * @return array|mixed
     */
    public function getCredentials(Request $request)
    {
        $credentials = [
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['email']
        );

        return $credentials;
    }

    /**
     * @param mixed                 $credentials
     * @param UserProviderInterface $userProvider
     *
     * @return User|object|UserInterface|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /**
         * The script below require :
         * - every user have unique mail inside the LDAP directory
         * - every user have uid that equal to its mail
         * feel free to modify this script to adapt to your LDAP server configuration.
         */
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);

        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        // LDAP host/port
        $ldaphost = 'localhost';
        $ldapport = 389;

        // login(mail)/password provided by user in the login form
        $login = $credentials['email'];
        $password = $credentials['password'];

        // LDAP rdn/password
        $ldaprdn = 'cn='.$login.',ou=People,dc=maxcrc,dc=com';
        $ldappass = $password;

        // using mail filter to search for user
        $filter = '(mail='.$login.')';

        // fetching user's attributes
        $attributes = array('mail');

        // using dn to search for user
        $ldaptree = 'ou=People,dc=maxcrc,dc=com';

        // connection to LDAP server
        $ldapconn = ldap_connect($ldaphost, $ldapport) or die('could not connect to');

        $user = null;

        if ($ldapconn) {
            // using LDAP v3
            ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);

            // Binding to LDAP with login/password
            $ldapbind = ldap_bind($ldapconn, $ldaprdn, $ldappass) or die('Error trying to bind:'.ldap_error($ldapconn));

            if ($ldapbind) {
                // searching to user by its uid=mail and return its value
                $result = ldap_search($ldapconn, $ldaptree, $filter, $attributes) or die('Error in search query:'.ldap_error($ldapconn));

                // returned data
                $data = ldap_get_entries($ldapconn, $result);

                if (1 == $data['count']) {
                    // fetch user from database by its email
                    $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $credentials['email']]);

                    if (!$user) {
                        $user = new User();

                        $user->setEmail($credentials['email']);
                        $user->setLdapUser(true);

                        $this->entityManager->persist($user);
                        $this->entityManager->flush();
                    }
                }
            } else {
                throw new CustomUserMessageAuthenticationException('LDAP bind failed ...');
            }
        }

        return $user;
    }

    /**
     * @param mixed         $credentials
     * @param UserInterface $user
     *
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    /**
     * Redirect authenticated user to its profile page.
     *
     * @param Request        $request
     * @param TokenInterface $token
     * @param string         $providerKey
     *
     * @return RedirectResponse|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return new RedirectResponse($this->urlGenerator->generate('company_homepage'));
    }

    /**
     * @return string
     */
    protected function getLoginUrl()
    {
        return $this->urlGenerator->generate('ldap_login');
    }
}
