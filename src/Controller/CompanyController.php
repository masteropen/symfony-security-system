<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/companies")
 */
class CompanyController extends AbstractController
{
    /**
     * @Route("/", name="company_homepage")
     */
    public function index()
    {
        return $this->render('company/companies.html.twig');
    }
}
