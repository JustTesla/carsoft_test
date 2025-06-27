<?php

namespace App\Repository;

use App\Entity\Service;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Service|null find($id, $lockMode = null, $lockVersion = null)
 * @method Service|null findOneBy(array $criteria, array $orderBy = null)
 * @method Service[]    findAll()
 * @method Service[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Service::class);
    }

    public function getChoices(string $ecuSoftwareId): \Generator
    {
        $queryBuilder = $this->createQueryBuilder('service');

        $queryBuilder
            ->leftJoin(
                'service.ecuSoftwareServices',
                'ecuSoftwareServices',
                Join::WITH,
                condition: 'IDENTITY(ecuSoftwareServices.ecuSoftware) = :id'
            )
            ->andWhere('ecuSoftwareServices IS NULL')
            ->setParameter('id', $ecuSoftwareId)
            ->addOrderBy('service.name', 'asc');

        foreach ($queryBuilder->getQuery()->getResult() as $service) {
            yield $service->getName() => $service;
        }
    }
}
