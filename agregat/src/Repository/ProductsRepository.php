<?php

namespace App\Repository;

use App\Entity\Products;
use App\Helper\Filter\ProductsFilter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
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

    public function findByTitle(string $search): array
    {
        $qb = $this->createQueryBuilder('p');
        return $qb
            ->where($qb->expr()->like('p.title', ':search'))
            ->setParameter(':search', '%' . $search . '%')
            ->getQuery()
            ->getResult();
    }

    public function findByKeyword(): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.keyWords is not null')
            ->getQuery()
            ->getResult();
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

    private function searchByName(QueryBuilder $qb, ?string $searchValue): void
    {
        $searchValue = preg_replace('/\s+/', ' ', $searchValue);
        if ($searchValue != ' ' && $searchValue != '') {
            $searchValue = explode(' ', trim($searchValue));
            $parameterForWhere = $qb->expr()->andX();
            foreach ($searchValue as $index => $word) {
                $parameterForWhere->add($qb->expr()->orX(
                    $qb->expr()->like('LOWER(p.title)', ':search' . $index),
                ));
                $qb->setParameter('search' . $index, "%" . mb_strtolower($word) . "%");
            }
            $qb->andWhere($parameterForWhere);
        }
    }


    public function getProductsByFilter(ProductsFilter $productsFilter): Paginator
    {
        $qb = $this->createQueryBuilder('p');
        if ($productsFilter->getName()) {
            $this->searchByName($qb, $productsFilter->getName());
            $qb
                ->orWhere($qb->expr()->like('p.article', ':article'))
                ->setParameter('article', '%' . $productsFilter->getName() . '%');
            $qb
                ->orWhere($qb->expr()->like('p.keyWords', ':keyWords'))
                ->setParameter('keyWords', '%' . $productsFilter->getName() . '%');
        }
        if ($productsFilter->getCategoryId()) {
            $qb
                ->join('p.categories', 'c')
                ->andWhere($qb->expr()->in('c.id', ':ids'))
                ->setParameter('ids', explode(',', $productsFilter->getCategoryId()));
        }
        if ($productsFilter->getSubCategoryId()) {
            $qb
                ->join('p.subCategories', 'sc')
                ->andWhere($qb->expr()->in('sc.id', ':ids'))
                ->setParameter('ids', explode(',', $productsFilter->getSubCategoryId()));
        }
        if ($productsFilter->getIsActual()) {
            $qb->andWhere('p.isActual = true');
        }
        if ($productsFilter->getMinPrice()) {
            $qb
                ->andWhere('p.price >= :minPrice')
                ->setParameter('minPrice', $productsFilter->getMinPrice());
        }
        if ($productsFilter->getMaxPrice()) {
            $qb
                ->andWhere('p.price <= :maxPrice')
                ->setParameter('maxPrice', $productsFilter->getMaxPrice());
        }
        if ($productsFilter->getMinRating()) {
            $qb
                ->andWhere('p.rating >= :minRating')
                ->setParameter('minRating', $productsFilter->getMinRating());
        }
        if ($productsFilter->getMaxRating()) {
            $qb
                ->andWhere('p.rating <= :maxRating')
                ->setParameter('maxRating', $productsFilter->getMaxRating());
        }
        return new Paginator($qb
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
