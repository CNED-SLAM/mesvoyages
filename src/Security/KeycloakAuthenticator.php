<?php
namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

/**
 * Description of KeycloakAuthenticator
 *
 * @author emds
 */
class KeycloakAuthenticator extends OAuth2Authenticator implements AuthenticationEntryPointInterface {
    
    private $clientRegistry;
    private $entityManager;
    private $router;
    
    public function __construct(ClientRegistry $clientRegistry,
            EntityManagerInterface $entityManager, RouterInterface $router) {
        $this->clientRegistry = $clientRegistry;
        $this->entityManager = $entityManager;
        $this->router = $router;
    }
    
    public function authenticate(Request $request): Passport {
        $client = $this->clientRegistry->getClient('keycloak');
//        var_dump($client);
        $accessToken = $this->fetchAccessToken($client);
//        dd($accessToken);
        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function() use ($accessToken, $client){
                /** @var KeycloakUser $keycloakUser */
                $keycloakUser = $client->fetchUserFromToken($accessToken);
                // 1) recherche du user dans la BDD à partir de son id Keycloak
                $existingUser = $this->entityManager
                        ->getRepository(User::class)
                        ->findOneBy(['keycloakId' => $keycloakUser->getId()]);
                if($existingUser){
                    return $existingUser;
                }
                // 2) le user existe mais n'est pas encore connecté avec Keycloak
                $email = $keycloakUser->getEmail();
                /** @var User $userInDatabase */
                $userInDatabase = $this->entityManager
                        ->getRepository(User::class)
                        ->findOneBy(['email' => $email]);
                if($userInDatabase){
                    $userInDatabase->setKeycloakId($keycloakUser->getId());
                    $this->entityManager->persist($userInDatabase);
                    $this->entityManager->flush();
                    return $userInDatabase;
                }
                // 3) le user n'existe pas encore dans la BDD
                $user = new User();
                $user->setKeycloakId($keycloakUser->getId());
                $user->setEmail($keycloakUser->getEmail());
                $user->setPassword("");
                $user->setRoles(['ROLE_ADMIN']);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                return $user;
            })
        );
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());
        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response {
        $targetUrl = $this->router->generate('admin.voyages');
        return new RedirectResponse($targetUrl);
    }

    public function start(Request $request, ?AuthenticationException $authException = null): Response {
        return new RedirectResponse(
                '/oauth/login', 
                Response::HTTP_TEMPORARY_REDIRECT
        );
    }

    public function supports(Request $request): ?bool {
        return $request->attributes->get('_route') === 'oauth_check';
    }
}
