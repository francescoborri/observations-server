<?php

namespace App\Repository;

use App\Entity\Observation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ObservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Observation::class);
    }

    public function findAllWithCriteria(string $field = null, string $sort = null, \DateTime $start = null, \DateTime $end = null)
    {
        $queryBuilder = $this->createQueryBuilder('observation');

        if (!is_null($field) && !is_null($sort))
            $queryBuilder->orderBy("observation.$field", $sort);

        if (!is_null($start))
            $queryBuilder
                ->andWhere('observation.datetime >= :start')
                ->setParameter('start', $start);

        if (!is_null($end))
            $queryBuilder
                ->andWhere('observation.datetime <= :end')
                ->setParameter('end', $end);

        return $queryBuilder
            ->getQuery()
            ->getResult();
    }
}
