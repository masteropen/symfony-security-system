<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Access to api data by users provided by a in_memory_provider provider (under config/packages/security.yml).
 *
 * @Route("/api")
 */
class ApiController extends AbstractController
{
    /**
     * List of available api data.
     *
     * @Route("/", name="api_homepage")
     */
    public function index()
    {
        return $this->render('api/index.html.twig');
    }

    /**
     * users data.
     *
     * @Route("/users", name="api_users")
     */
    public function users()
    {
        return $this->render('api/json/users.json.twig');
    }
}
