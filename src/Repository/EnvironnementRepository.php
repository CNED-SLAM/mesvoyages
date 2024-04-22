<?php

namespace App\Repository;

use App\Entity\Environnement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Environnement>
 *
 * @method Environnement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Environnement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Environnement[]    findAll()
 * @method Environnement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnvironnementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Environnement::class);
    }
    
    /**
     * Supprime un environnement
     * @param Environnement $environnement
     * @return void
     */
    public function remove(Environnement $environnement): void
    {
        $this->getEntityManager()->remove($environnement);
        $this->getEntityManager()->flush();
    }    

    /**
     * Ajoute un environnement
     * @param Environnement $environnement
     * @return void
     */
    public function add(Environnement $environnement): void
    {
        $this->getEntityManager()->persist($environnement);
        $this->getEntityManager()->flush();
    }    

}
