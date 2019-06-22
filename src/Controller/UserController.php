<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
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

    /**
     * @Route("/test", name="test")
     */
    public function test(){
        $user = new User();
        $user->setEmail('co.cc@test.com');
        $user->setRoles(array("ROLE_USER"));
        $user->setLdapUser(0);

        $this->getDoctrine()->getManager()->persist($user);
        $this->getDoctrine()->getManager()->flush();

        dd('ok');
    }

    /**
     * @Route("/check", name="check")
     */
    public function check(Request $request){

        if($request->isXmlHttpRequest()){

            $email = $request->request->get('email');

            $user = $this->getDoctrine()->getManager()->getRepository(User::class)->findOneBy(array('email' => $email));

            if(!$user){
                return new JsonResponse(array('exist' => 0));
            }
            return new JsonResponse(array('exist' => 1));

        }

    }

}
