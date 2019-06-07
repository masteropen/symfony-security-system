<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/profile", name="app_profile")
     */
    public function profile()
    {
        return $this->render('user/profile.html.twig', array());
    }


    /**
     * @Route("/user/create", name="app_create_user")
     *
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function createUser(UserPasswordEncoderInterface $passwordEncoder)
    {
    	$user = new User();

    	$user->setEmail('noureddine-majid@hotmail.fr');
    	$user->setPassword($passwordEncoder->encodePassword(
             $user,
             '123'
         ));

    	$entityManager = $this->getDoctrine()->getManager();

    	$entityManager->persist($user);
    	$entityManager->flush();

    	var_dump('user created'); die();

    }

}
