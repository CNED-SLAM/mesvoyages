<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    const PAGE_LOGIN = "login/index.html.twig";
    
    /**
     * @Route("/login", name="login")
     */
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // récupération éventuelle de l'erreur
        $error = $authenticationUtils->getLastAuthenticationError();
        // récupération éventuelledu dernier nom de login utilisé
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render(self::PAGE_LOGIN, [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }
    
    /**
     * @Route("/logout", name="logout")
     */
    public function logout(){

    }
}
