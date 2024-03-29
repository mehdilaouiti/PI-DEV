<?php

namespace App\Repository;

use App\Entity\Livraison;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Livraison|null find($id, $lockMode = null, $lockVersion = null)
 * @method Livraison|null findOneBy(array $criteria, array $orderBy = null)
 * @method Livraison[]    findAll()
 * @method Livraison[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LivraisonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livraison::class);
    }

    // /**
    //  * @return Livraison[] Returns an array of Livraison objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Livraison
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function listOrderByDate()
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.DateLiv', 'DESC')
            ->orderBy('s.id', 'DESC')
            ->getQuery()->getResult();
    }
    public function listOrderByNom ()
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.Livreur', 'DESC')
            ->getQuery()->getResult();
    }
    public function findTotalLivraison()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT l.id,l.NomLiv,
            count(li.id) as TotalLivraison
            FROM App\Entity\Livraison li, App\Entity\Livreur l
            WHERE li.Livreur=l.id
            GROUP BY l.id
            ORDER by count(li.id) DESC 
            '
        );
        return $query->getResult();
    }

}
