<?php
namespace App\Controller;

use App\Repository\VisiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Description of VoyagesConrtoller
 *
 * @author emds
 */
class VoyagesController extends AbstractController{

    const PAGE_VOYAGES = "pages/voyages.html.twig";
    const PAGE_VOYAGE = "pages/voyage.html.twig";
    const ROUTE_VOYAGES = "voyages";
    
    /**
     * 
     * @var VisiteRepository
     */
    private $repository;
    
    /**
     * 
     * @param VisiteRepository $repository
     */
    public function __construct(VisiteRepository $repository) {
        $this->repository = $repository;
    }
    
    /**
     * @Route("/voyages", name="voyages")
     * @return Response
     */
    public function index(): Response{
        $visites = $this->repository->findAllOrderBy('datecreation', 'DESC');
        return $this->render(self::PAGE_VOYAGES, [
            'visites' => $visites
        ]);
    }
    
    /**
     * @Route("/voyages/tri/{champ}/{ordre}", name="voyages.sort")
     * @param type $champ
     * @param type $ordre
     * @return Response
     */
    public function sort($champ, $ordre): Response{
        $visites = $this->repository->findAllOrderBy($champ, $ordre);
        return $this->render(self::PAGE_VOYAGES, [
            'visites' => $visites
        ]);
    }
    
    /**
     * @Route("/voyages/recherche/{champ}", name="voyages.findallequal")
     * @param type $champ
     * @param Request $request
     * @return Response
     */
    public function findAllEqual($champ, Request $request): Response{
        if($this->isCsrfTokenValid('filtre_'.$champ, $request->get('_token'))){
            $valeur = $request->get("recherche");
            $visites = $this->repository->findByEqualValue($champ, $valeur);
            return $this->render(self::PAGE_VOYAGES, [
                'visites' => $visites
            ]);
        }
        return $this->redirectToRoute(self::ROUTE_VOYAGES);
    }
    
    /**
     * @Route("/voyages/voyage/{id}", name="voyages.showone")
     * @param type $id
     * @return Response
     */
    public function showOne($id): Response{
        $visite = $this->repository->find($id);
        return $this->render(self::PAGE_VOYAGE, [
            'visite' => $visite
        ]);
    }
}
