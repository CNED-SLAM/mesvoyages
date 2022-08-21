<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OAuthController extends AbstractController
{
    /**
     * @Route("/oauth/login", name="oauth_login")
     */
    public function index(ClientRegistry $clientRegistry): RedirectResponse{
        return $clientRegistry->getClient('keycloak')->redirect();
    }
    
    /**
     * @Route("/oauth/callback", name="oauth_check")
     */    
    public function connectCheckAction(Request $request, ClientRegistry $clientRegistry){

    }
    
    /**
     * @Route("/logout", name="logout")
     */    
    public function logout(){
        
    }
    
}

