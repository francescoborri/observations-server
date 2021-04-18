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

    public function findAllWithCriteria(string $field = null, string $sort = null, \DateTime $start = null, \DateTime $end = null, int $day = null, int $month = null, int $year = null, int $results = null)
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

        if (!is_null($day))
            $queryBuilder
                ->andWhere('DAY(observation.datetime) = :day')
                ->setParameter('day', $day);

        if (!is_null($month))
            $queryBuilder
                ->andWhere('MONTH(observation.datetime) = :month')
                ->setParameter('month', $month);

        if (!is_null($year))
            $queryBuilder
                ->andWhere('YEAR(observation.datetime) = :year')
                ->setParameter('year', $year);
        
        if (!is_null($results))
            $queryBuilder->setMaxResults($results);

        return $queryBuilder
            ->getQuery()
            ->getResult();
    }
}
