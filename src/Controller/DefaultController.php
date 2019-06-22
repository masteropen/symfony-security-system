<?php

namespace App\Controller;

use App\Entity\Operation;
use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Redirect to user's profile page or login form
 *
 * @Route("/")
 */
class DefaultController extends AbstractController
{

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var ObjectManager
     */
    private $om;

    public function __construct(ObjectManager $om, SessionInterface $session)
    {
        $this->om = $om;
        $this->session = $session;
    }

    /**
     * @Route("/", name="homepage")
     */
    public function index()
    {
        dd($this->om);
        dd($this->getDoctrine()->getManager());

        return $this->getUser() ? $this->redirectToRoute('app_profile') : $this->redirectToRoute('app_login');
    }

    /**
     * List of user's operations
     *
     * @return Response
     *
     * @Route("/dashboard", name="dashboard")
     */
    public function dashboard()
    {
        $operations = $this->om->getRepository(Operation::class)->findAll();

        return $this->render('dashboard.html.twig', array(
            'operations' => $operations
        ));
    }

    /**
     * synthesis of an existent operation
     *
     * @param Operation $operation
     * @return Response
     *
     * @Route("/{id}/operation/synthesis", name="synthesis")
     */
    public function synthesis(Operation $operation)
    {
        $this->session->set('currentOperation', $operation);

        return $this->render('synthesis.html.twig', array(
            'operation' => $operation
        ));
    }

    /**
     * List of operation's lots
     *
     * @param Operation $operation
     * @return Response
     *
     * @Route("/{id}/operation/listOfLots", name="listOfLots")
     */
    public function listOfLots(Operation $operation)
    {
        return $this->render('list_lots.html.twig',array(
            'operation' => $operation
        ));

    }

    /**
     * Generate a set of faker operations
     *
     * @Route("operation/fixtures", name="add_operations")
     */
    public function addOperations()
    {
        for ($i=0; $i<20; $i++){
            $operation = new Operation();
            $operation->setName('operation '.$i);
            $this->om->persist($operation);
            $this->om->flush();
        }
        dd('ok');
    }

}
