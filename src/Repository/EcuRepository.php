<?php

namespace App\Repository;

use App\Entity\Ecu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Ecu|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ecu|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ecu[]    findAll()
 * @method Ecu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EcuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ecu::class);
    }

    public function getChoices(): \Generator
    {
        foreach ($this->findAll() as $service) {
            yield $service->getName() => $service;
        }
    }
}
