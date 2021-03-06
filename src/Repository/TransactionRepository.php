<?php

namespace App\Repository;

use App\Entity\Filter;
use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function getFarmersByProduct(string $category)
    {
        return $this->createQueryBuilder('t')
            ->select('c.city, c.latitude, c.longitude')
            ->join('App\Entity\Farmer', 'f', 'WITH', 'f.id = t.farmer')
            ->join('App\Entity\City', 'c', 'WITH', 'c.id = f.city')
            ->join('App\Entity\Product', 'p', 'WITH', 'p.id = t.product')
            ->where('p.category = :category')
            ->setParameter('category', $category)
            ->getQuery()
            ->getResult();
    }

    public function getTransactionData($buyer)
    {
        return $this->createQueryBuilder('t')
            ->select('p.name, avg(t.price) as avgprice, sum(t.quantity) as quantity')
            ->join('App\Entity\Product', 'p', 'WITH', 'p.id = t.product')
            ->where('t.buyer = :buyer')
            ->setParameter('buyer', $buyer)
            ->groupBy('p.name')
            ->orderBy('quantity', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }

    public function getTransactionTotal($buyer)
    {
        return $this->createQueryBuilder('t')
            ->select('sum(t.quantity) as total')
            ->where('t.buyer = :buyer')
            ->setParameter('buyer', $buyer)
            ->getQuery()
            ->getOneOrNullResult();
    }


    // /**
    //  * @return Transaction[] Returns an array of Transaction objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Transaction
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
