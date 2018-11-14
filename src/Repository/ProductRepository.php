<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class ProductRepository extends EntityRepository
{
    /**
     * @param $limit
     * @param $offset
     * @return array
     */
    public function findByLimitAndOffset($limit, $offset)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->setMaxResults($limit)
            ->setFirstResult($offset * $limit);

        $qb->getQuery()->useResultCache(true, 360)->useQueryCache(true);

        return $qb->getQuery()->getResult();
    }

    /**
     * @return int
     */
    public function getProductsCount()
    {
        $qb = $this->createQueryBuilder('p')
            ->select('COUNT(p.id)');

        $qb->getQuery()->useResultCache(true, 360)->useQueryCache(true);

        return $qb->getQuery()->getSingleScalarResult();
    }
}
