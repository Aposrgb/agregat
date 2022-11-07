<?php

namespace App\Repository;

use App\Entity\News;
use App\Helper\Filter\NewsFilter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<News>
 *
 * @method News|null find($id, $lockMode = null, $lockVersion = null)
 * @method News|null findOneBy(array $criteria, array $orderBy = null)
 * @method News[]    findAll()
 * @method News[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, News::class);
    }

    public function getNewsByFilter(NewsFilter $newsFilter): Paginator
    {
        $qb = $this->createQueryBuilder('n');

        if ($newsFilter->getYear()) {
            $startDate = new \DateTime(date('Y-m-d', strtotime('first day of january ' . $newsFilter->getYear())));
            $endDate = new \DateTime(date('Y-m-d', strtotime('last day of december ' . $newsFilter->getYear())));
            $endDate->setTime(23, 59);
            $qb
                ->where('n.createdAt >= :startDate')
                ->andWhere('n.createdAt <= :endDate')
                ->setParameter('startDate', $startDate)
                ->setParameter('endDate', $endDate);
        }

        return new Paginator($qb
            ->orderBy('n.createdAt', 'DESC')
            ->setFirstResult($newsFilter->getPagination()->getFirstMaxResult())
            ->setMaxResults($newsFilter->getPagination()->getLimit())
        );
    }

    public function save(News $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(News $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return News[] Returns an array of News objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('n.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?News
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
