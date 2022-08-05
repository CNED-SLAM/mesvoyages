<?php
namespace App\Controller\admin;

use App\Entity\Environnement;
use App\Repository\EnvironnementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Description of AdminEnvironnementController
 *
 * @author emds
 */
class AdminEnvironnementController extends AbstractController {
    
    const PAGE_ADMIN_ENVIRONNEMENTS = "admin/admin.environnements.html.twig";
    const ROUTE_ENVIRONNEMENTS = "admin.environnements";

    /**
     * 
     * @var VisiteRepository
     */
    private $repository;

    /**
     * 
     * @param EnvironnementRepository $repository
     */
    public function __construct(EnvironnementRepository $repository) {
        $this->repository = $repository;
    }
    
    /**
     * @Route("/admin/environnements", name="admin.environnements")
     * @return Response
     */
    public function index(): Response{
        $environnements = $this->repository->findAll();
        return $this->render(self::PAGE_ADMIN_ENVIRONNEMENTS, [
            'environnements' => $environnements
        ]);
    }
    
    /**
     * @Route("/admin/environnement/suppr/{id}", name="admin.environnement.suppr")
     * @param Environnement $environnement
     * @return Response
     */
    public function suppr(Environnement $environnement): Response{
        $this->repository->remove($environnement, true);
        return $this->redirectToRoute(self::ROUTE_ENVIRONNEMENTS);
    }
    
    /**
     * @Route("/admin/environnement/ajout", name="admin.environnement.ajout")
     * @param Request $request
     * @return Response
     */
    public function ajout(Request $request): Response{
        $nomEnvironnement = $request->get("nom");
        $environnement = new Environnement();
        $environnement->setNom($nomEnvironnement);
        $this->repository->add($environnement, true);
        return $this->redirectToRoute(self::ROUTE_ENVIRONNEMENTS);       
    }        
    
}
