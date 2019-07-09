<?php

namespace App\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Redirect to user's profile page or login form.
 *
 * @Route("/")
 */
class DefaultController extends AbstractController
{
    /**
     * @var ObjectManager
     */
    private $om;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * @Route("/", name="homepage")
     */
    public function index()
    {
        return $this->getUser() ? $this->redirectToRoute('app_profile') : $this->redirectToRoute('app_login');
    }
}
