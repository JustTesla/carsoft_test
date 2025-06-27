<?php

namespace App\Repository;

use App\Entity\EcuSoftware;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EcuSoftware|null find($id, $lockMode = null, $lockVersion = null)
 * @method EcuSoftware|null findOneBy(array $criteria, array $orderBy = null)
 * @method EcuSoftware[]    findAll()
 * @method EcuSoftware[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EcuSoftwareRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EcuSoftware::class);
    }
}
