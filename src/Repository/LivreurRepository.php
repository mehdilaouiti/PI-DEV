<?php

namespace App\Repository;

use App\Entity\Livreur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Livreur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Livreur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Livreur[]    findAll()
 * @method Livreur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LivreurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livreur::class);
    }
    public function SearchName($data)
    {
        return $this->createQueryBuilder('m')
            ->where('m.NomLiv LIKE :data')->orWhere('m.PrenomLiv Like :data ')->orWhere('m.numLiv Like :data ')
            ->setParameter('data', '%'.$data.'%')
            ->getQuery()->getResult()
            ;
    }

    // /**
    //  * @return Livreur[] Returns an array of Livreur objects
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
    public function findOneBySomeField($value): ?Livreur
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
