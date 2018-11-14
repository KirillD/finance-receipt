<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    /**
     * @param string $login
     * @return User|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneUserByEmailOrUsername(string $login): ?User
    {
        $qb = $this->createQueryBuilder('u');
        $qb->where('u.emailCanonical = :email OR u.username = :username')
            ->setParameter('email', strtolower($login))
            ->setParameter('username', $login);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
