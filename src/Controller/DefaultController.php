<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Redirect to user's profile page or login form
 *
 * @Route("/")
 */
class DefaultController extends AbstractController
{

    /**
     * @Route("/", name="homepage")
     */
    public function index()
    {
        return $this->getUser() ? $this->redirectToRoute('app_profile') : $this->redirectToRoute('app_login');
    }

}
