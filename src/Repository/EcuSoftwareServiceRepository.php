<?php

namespace App\Repository;

use App\Entity\EcuSoftwareService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EcuSoftwareService|null find($id, $lockMode = null, $lockVersion = null)
 * @method EcuSoftwareService|null findOneBy(array $criteria, array $orderBy = null)
 * @method EcuSoftwareService[]    findAll()
 * @method EcuSoftwareService[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EcuSoftwareServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EcuSoftwareService::class);
    }
}
