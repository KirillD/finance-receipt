<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class TurnoverRepository extends EntityRepository
{
    /**
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findLastTurnover()
    {
        $qb = $this->createQueryBuilder('t')
            ->orderBy('t.id', 'DESC')
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
