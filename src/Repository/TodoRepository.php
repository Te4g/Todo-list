<?php

namespace App\Repository;

use App\Entity\Todo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Todo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Todo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Todo[]    findAll()
 * @method Todo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TodoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Todo::class);
    }


    public function findByUserId($userId)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.user = :id')
            ->andWhere('t.is_completed = :false' )
/*            ->andWhere('t.is_completed = :null' )*/
            ->setParameter('id', $userId)
            ->setParameter('false', false)
/*            ->setParameter('null', null)*/
            ->orderBy('t.created_at', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
    public function findByUserIdOld($userId)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.user = :id')
            ->andWhere('t.is_completed = :true' )
            ->setParameter('id', $userId)
            ->setParameter('true', true)
            ->orderBy('t.created_at', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }


    /*
    public function findOneBySomeField($value): ?Todo
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
