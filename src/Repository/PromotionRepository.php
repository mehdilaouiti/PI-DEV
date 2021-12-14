<?php

namespace App\Repository;

use App\Entity\Promotion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Promotion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Promotion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Promotion[]    findAll()
 * @method Promotion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PromotionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Promotion::class);
    }

    // /**
    //  * @return Promotion[] Returns an array of Promotion objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Promotion
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function SearchName($data)
    {
        return $this->createQueryBuilder('m')
            ->where('m.nompromotion LIKE :data')
            ->setParameter('data', '%'.$data.'%')
            ->getQuery()->getResult()
            ;
    }

    public function listOrderByRemise()
    {

        return $this->createQueryBuilder('s')
            ->orderBy('s.remise','DESC')
            ->getQuery()->getResult();

    }

    public function DateExpr()
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.date >   :date1')
            ->setParameter('date1', new \DateTime('now'))
            ->getQuery()->getResult();}
}
