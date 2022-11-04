<?php

namespace App\Repository;

use App\Entity\User;
use App\Helper\EnumRoles\UserRoles;
use App\Helper\EnumStatus\PurchaseStatus;
use App\Helper\EnumStatus\UserStatus;
use App\Helper\EnumType\PurchaseType;
use App\Helper\Filter\UserFilter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function searchColleague(UserFilter $userFilter): array
    {
        $qb = $this->createQueryBuilder('u');
        if ($userFilter->getFio()) {
            $this->searchByName($qb, $userFilter->getFio());
        }
        $qb = $this->getConfirmedUserQB($qb)
            ->orderBy('u.surname', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function findAllUsersInSite(): array
    {
        $qb = $this->createQueryBuilder('u');
        foreach (UserRoles::getSitesRoles() as $index => $role) {
            $qb
                ->orWhere($qb->expr()->like('u.roles', ':role' . $index))
                ->setParameter('role' . $index, '%' . $role . '%');
        }
        return $qb
            ->andWhere('u.status = :statusActive')
            ->setParameter('statusActive', UserStatus::CONFIRMED->value)
            ->getQuery()
            ->getResult();
    }

    public function findByEmail($email): mixed
    {
        return $this->createQueryBuilder('u')
            ->where('LOWER(u.email) = :findEmail')
            ->setParameter('findEmail', mb_strtolower($email))
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findUsersByPurchasesMemberAssociation()
    {
        return $this->findUsersWithMemberAssociationPurchase()
            ->andWhere('p.datePurchase < :date')
            ->setParameter('date', (new \DateTime())->modify('-1 year'))
            ->getQuery()
            ->getResult();
    }

    public function findUsersWithMemberAssociationExpiredFourteenDays()
    {
        $startDate = (new \DateTime())->modify('-1 year')->modify('+14 days')->setTime(0, 0);
        $endDate = (clone $startDate)->setTime(23, 59);
        return $this->findUsersWithMemberAssociationPurchase()
            ->andWhere('p.datePurchase >= :startDate')
            ->andWhere('p.datePurchase <= :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getResult();
    }

    private function findUsersWithMemberAssociationPurchase(): QueryBuilder
    {
        return $this->createQueryBuilder('u')
            ->join('u.purchases', 'p')
            ->where('p.status = :status')
            ->andWhere('p.type = :type')
            ->setParameter('status', PurchaseStatus::PURCHASED->value)
            ->setParameter('type', PurchaseType::SUBSCRIPTION->value);
    }

    public function findAllUsersWtihoutApprovedDiplom($countWeek): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.status != :statusBlocked')
            ->setParameter('statusBlocked', UserStatus::BLOCKED->value)
            ->andWhere('u.status != :statusConfirmed')
            ->setParameter('statusConfirmed', UserStatus::CONFIRMED->value)
            ->andWhere('u.createdAt < :date')
            ->setParameter('date', (new \DateTime())->modify("$countWeek week"))
            ->getQuery()
            ->getResult();
    }

    public function searchLecturer(UserFilter $userFilter): Paginator
    {
        $qb = $this->createQueryBuilder('u');
        $this->searchByUserFilter($qb, $userFilter);
        $paginationFilter = $userFilter->getPagination();
        return new Paginator(
            $qb
                ->andWhere($qb->expr()->like('u.roles', ':roleLecturer'))
                ->setParameter('roleLecturer', "%" . UserRoles::ROLE_LECTURER->value . "%")
                ->andWhere('u.availableInSearch = true')
                ->andWhere('u.status = :confirmed')
                ->setParameter('confirmed', UserStatus::CONFIRMED->value)
                ->setFirstResult($paginationFilter->getFirstMaxResult())
                ->setMaxResults($paginationFilter->getLimit())
        );
    }

    public function getConfirmedUserQB(QueryBuilder $qb): QueryBuilder
    {
        return $qb
            ->join('u.diploma', 'd')
            ->andWhere('d.diplomChecked = true')
            ->andWhere('u.status = :confirmed')
            ->setParameter('confirmed', UserStatus::CONFIRMED->value);
    }

    public function searchDoctor(UserFilter $userFilter): Paginator
    {
        $qb = $this->createQueryBuilder('u')
            ->andWhere('u.availableInSearch = true');
        $this->searchByUserFilter($qb, $userFilter);
        $paginationFilter = $userFilter->getPagination();
        return new Paginator(
            $this->getConfirmedUserQB($qb)
                ->setFirstResult($paginationFilter->getFirstMaxResult())
                ->setMaxResults($paginationFilter->getLimit())
        );
    }

    public function searchUserByEmail($email)
    {
        $result = $this->createQueryBuilder('u')
            ->where('u.email = :email')
            ->orWhere('u.emailAdminPanel = :email')
            ->setParameter('email', $email)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
        return is_array($result) && !empty($result) ? $result[0] : null;
    }

    public function findByIdsModerators(array $ids): array
    {
        $qb = $this->getQueryBuilderFindByIds($ids);
        return $qb
            ->andWhere($qb->expr()->like('u.roles', ':role'))
            ->setParameter('role', "%" . UserRoles::ROLE_MODERATOR->value . "%")
            ->getQuery()
            ->getResult();
    }

    public function findByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }
        return $this->getQueryBuilderFindByIds($ids)
            ->getQuery()
            ->getResult();
    }

    private function getQueryBuilderFindByIds(array $ids): QueryBuilder
    {
        $qb = $this->createQueryBuilder('u');
        return $qb
            ->where($qb->expr()->in('u.id', ':ids'))
            ->setParameter('ids', $ids)
            ->andWhere('u.status = :status')
            ->setParameter('status', UserStatus::CONFIRMED->value);
    }

    public function findUserForReport(UserFilter $userFilter): array
    {
        $qb = $this->createQueryBuilder("u");
        if ($date = $userFilter->getDate()) {
            $startDate = (new \DateTime($date))->setTime(0, 0);
            $endDate = (new \DateTime($date))->setTime(23, 59);
            $qb
                ->andWhere('u.dateRegistration >= :startDate ')
                ->andWhere('u.dateRegistration <= :endDate ')
                ->setParameter('startDate', $startDate)
                ->setParameter('endDate', $endDate);
        }
        if ($eventId = $userFilter->getEvent()) {
            $qb
                ->join('u.purchases', 'pr')
                ->andWhere('pr.event = :eventId')
                ->andWhere('pr.status = :confirmed')
                ->setParameter('confirmed', PurchaseStatus::PURCHASED->value)
                ->setParameter('eventId', $eventId);
        }
        if ($filterValue = $userFilter->getStatus()) {
            $qb
                ->andWhere('u.status = :status')
                ->setParameter('status', $filterValue);
        }
        if ($filterValue = $userFilter->getRegion()) {
            $qb
                ->join('u.nearestRepresentative', 'r')
                ->andWhere('r.id = :region')
                ->setParameter('region', $filterValue);
        }
        return $qb->getQuery()->getResult();
    }

    public function getPaginationFilter(UserFilter $userFilter): Paginator
    {
        $qb = $this->createQueryBuilder("u");

        if ($filterValue = $userFilter->getStatus()) {
            $qb
                ->andWhere('u.status = :status')
                ->setParameter('status', $filterValue);
        }
        if ($filterValue = $userFilter->getRegion()) {
            $qb
                ->join('u.nearestRepresentative', 'r')
                ->andWhere('r.id = :region')
                ->setParameter('region', $filterValue);
        }
        if ($filterValue = $userFilter->getBelongProgram()) {
            $qb
                ->join('u.purchasedPrograms', 'p')
                ->andWhere('p.id = :purchasedProgram')
                ->setParameter('purchasedProgram', $filterValue);
        }
        if ($filterValue = $userFilter->getParticipatedInEvent()) {
            $qb
                ->andWhere('u.isParticipatedEvent = :isEvent')
                ->setParameter('isEvent', $filterValue);
        }
        if ($filterValue = $userFilter->getRole()) {
            $qb
                ->andWhere($qb->expr()->like('u.roles', ':role'))
                ->setParameter('role', "%" . $filterValue . "%");
        }
        if ($filterValue = $userFilter->getSpecialization()) {
            $qb
                ->andWhere($qb->expr()->like('u.specialization', ':specialization'))
                ->setParameter('specialization', "%" . $filterValue . "%");
        }
        if ($searchValue = $userFilter->getEmail()) {
            $qb
                ->andWhere($qb->expr()->like('u.email', ':search'))
                ->setParameter('search', "%" . $searchValue . "%");
        }
        if ($searchValue = $userFilter->getFio()) {
            $this->searchByName($qb, $searchValue);
        }
        if ($searchValue = $userFilter->getPhone()) {
            $qb
                ->andWhere($qb->expr()->like('u.phone', ':search'))
                ->setParameter('search', "%" . $searchValue . "%");
        }

        $paginationFilter = $userFilter->getPagination();
        return new Paginator($qb
            ->setFirstResult($paginationFilter->getFirstMaxResult())
            ->setMaxResults($paginationFilter->getLimit())
        );
    }

    private function searchByUserFilter(QueryBuilder $qb, UserFilter $userFilter)
    {
        if ($searchValue = $userFilter->getFio()) {
            $this->searchByName($qb, $searchValue);
        }
        if ($userFilter->getMemberAssociation()) {
            $qb
                ->andWhere($qb->expr()->like('u.roles', ':roleMember'))
                ->setParameter('roleMember', "%" . UserRoles::ROLE_MEMBER_ASSOCIATION->value . "%");
        }
        if ($userFilter->getGraduateStudent()) {
            $qb
                ->andWhere($qb->expr()->like('u.roles', ':roleGraduate'))
                ->setParameter('roleGraduate', "%" . UserRoles::ROLE_GRADUATE_STUDENT->value . "%");
        }
        if ($searchValue = $userFilter->getRegion()) {
            $qb
                ->join('u.nearestRepresentative', 'r')
                ->andWhere($qb->expr()->in('r.id', ':region'))
                ->setParameter('region', explode(',', $searchValue));
        }
        if ($searchValue = $userFilter->getProfInterest()) {
            $qb
                ->join('u.profInterests', 'p')
                ->andWhere($qb->expr()->in('p.id', ':profInterest'))
                ->setParameter('profInterest', explode(',', $searchValue));
        }
        if ($filterValue = $userFilter->getSpecialization()) {
            $qb
                ->join('u.specialization', 'sp')
                ->andWhere($qb->expr()->in('sp.id', ':specializationId'))
                ->setParameter('specializationId', explode(',', $filterValue));
        }
    }

    private function searchByName(QueryBuilder $qb, ?string $searchValue): void
    {
        $searchValue = preg_replace('/\s+/', ' ', $searchValue);
        if ($searchValue != ' ' && $searchValue != '') {
            $searchValue = explode(' ', trim($searchValue));
            $parameterForWhere = $qb->expr()->orX();
            foreach ($searchValue as $index => $word) {
                $parameterForWhere->add($qb->expr()->orX(
                    $qb->expr()->like('LOWER(u.surname)', ':search' . $index),
                    $qb->expr()->like('LOWER(u.firstname)', ':search' . $index),
                    $qb->expr()->like('LOWER(u.patronymic)', ':search' . $index)
                ));
                $qb->setParameter('search' . $index, "%" . mb_strtolower($word) . "%");
            }
            $qb->andWhere($parameterForWhere);
        }
    }

    public function add(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
