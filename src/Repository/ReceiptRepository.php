<?php

namespace App\Repository;

use App\Entity\Receipt\Receipt;
use DateTime;
use Doctrine\ORM\EntityRepository;

class ReceiptRepository extends EntityRepository
{
    /**
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getFirstFinishedReceipt()
    {
        $qb = $this->createQueryBuilder('r')
            ->where('r.status = :status')
            ->setParameter('status', Receipt::STATUS_FINISHED)
            ->orderBy('r.id', 'DESC')
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param DateTime $dateTime
     * @return array
     */
    public function findFinishedReceiptsInPeriod(DateTime $dateTime)
    {
        $dateTo = clone $dateTime;
        $dateTo->modify('+1 hour');
        $qb = $this->createQueryBuilder('r')
            ->where('r.status = :status')
            ->setParameter('status', Receipt::STATUS_FINISHED)
            ->andWhere('r.finishedAt BETWEEN :from AND :to')
            ->setParameter('from', $dateTime)
            ->setParameter('to', $dateTo);

        return $qb->getQuery()->getResult();
    }
}
