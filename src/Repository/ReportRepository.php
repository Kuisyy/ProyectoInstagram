<?php

namespace App\Repository;

use App\Entity\Report;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Report::class);
    }

    public function findUnresolvedReports(): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.resolved = :resolved')
            ->setParameter('resolved', false)
            ->orderBy('r.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
} 