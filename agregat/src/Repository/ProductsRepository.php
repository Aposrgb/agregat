<?php

namespace App\Repository;

use App\Entity\Products;
use App\Helper\Filter\ProductsFilter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Products>
 *
 * @method Products|null find($id, $lockMode = null, $lockVersion = null)
 * @method Products|null findOneBy(array $criteria, array $orderBy = null)
 * @method Products[]    findAll()
 * @method Products[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Products::class);
    }

    public function getProductWithIds(ProductsFilter $productsFilter, array $ids): Paginator
    {
        $qb = $this->createQueryBuilder('p');
        return new Paginator($qb
            ->where($qb->expr()->in('p.id', $ids))
            ->orderBy('p.id', 'ASC')
            ->setFirstResult($productsFilter->getPagination()->getFirstMaxResult())
            ->setMaxResults($productsFilter->getPagination()->getLimit())
        );
    }

    public function getProductsByFilter(ProductsFilter $productsFilter): Paginator
    {
        return new Paginator($this->createQueryBuilder('p')
            ->orderBy('p.id', 'ASC')
            ->setFirstResult($productsFilter->getPagination()->getFirstMaxResult())
            ->setMaxResults($productsFilter->getPagination()->getLimit())
        );

    }

    public function save(Products $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Products $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Products[] Returns an array of Products objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Products
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
