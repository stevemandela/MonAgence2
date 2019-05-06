<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;




class SecurityController extends AbstractController
{


        /**
         * @Route("/login", name="login")
         */
        public function login(AuthenticationUtils  $authenticationUtils)
        {
            $LastUsername = $authenticationUtils->getLastUsername();
            $error = $authenticationUtils->getLastAuthenticationError();
            return $this->render('security/login.html.twig', [
                'Last_username' => $LastUsername,
                'error' => $error
            ]);
        }
}