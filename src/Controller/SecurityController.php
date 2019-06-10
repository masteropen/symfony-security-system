<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Login/Logout users provided by app_user_provider provider (under config/packages/security.yml)
 */
class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     *
     * @throws \Exception
     */
    public function logout()
    {
        throw new \Exception('bad logout configuration');

    }

    /**
     * @Route("/login_ldap", name="ldap_login")
     */
    public function loginToLdap(Request $request, AuthenticationUtils $authUtils)
    {
        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();

        return $this->render('security/login_ldap.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }

    /**
     * @Route("/logout_ldap", name="ldap_logout")
     *
     * @throws \Exception
     */
    public function logoutFromLdap()
    {
        throw new \Exception('bad logout configuration');

    }

    /**
     * @Route("/login_test_2", name="login_2")
     */
    public function login2(){

        /**
         * The script below require :
         * - every user have unique mail inside the LDAP directory
         * - every user have uid that equal to its mail
         * feel free to modify this script to adapt to your LDAP server configuration
         */

        // LDAP host/port
        $ldaphost = "localhost";
        $ldapport = 389;

        // login(mail)/password provided by user in the login form
        $login = "panji.pratomo555@gmail.com";
        $password = "SomePassword";

        // LDAP rdn/password
        $ldaprdn = 'cn='.$login.',ou=People,dc=maxcrc,dc=com';
        $ldappass = $password;

        // using mail filter to search for user
        $filter = "(mail=".$login.")";

        // fetching user's attributes
        $attributes = array("mail");

        // using dn to search for user
        $ldaptree = "ou=People,dc=maxcrc,dc=com";

        // connection to LDAP server
        $ldapconn = ldap_connect($ldaphost,$ldapport) or die('could not connect to');


        if ($ldapconn) {

            // using LDAP v3
            ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);

            // Binding to LDAP with login/password
            $ldapbind = ldap_bind($ldapconn, $ldaprdn, $ldappass) or die("Error trying to bind:".ldap_error($ldapconn));

            if ($ldapbind) {

                // searching to user by its uid=mail and return its value
                $result = ldap_search($ldapconn, $ldaptree, $filter, $attributes) or die("Error in search query:".ldap_error($ldapconn));

                // returned data
                $data = ldap_get_entries($ldapconn, $result);

                if($data["count"] == 1){

                    // the mail of returned user
                    echo "this user exists in LDAP and have this mail : ";
                    dd($data[0]["mail"][0]);

                    // do something with this user ...

                }

                echo "user dont exists in LDAP, or the request matches more than one user !";

                // number of returned results
                // $data["count"]

                // number of finding attributes
                // $attrs["count"]

            } else {
                echo "LDAP bind failed ...";
            }
        }

        echo ("<h3>Script Done ! </h3>"); die();
    }

}
