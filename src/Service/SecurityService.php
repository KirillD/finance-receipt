<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityService
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * SecurityService constructor.
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $encoder
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $encoder,
        TokenStorageInterface $tokenStorage
    ) {
        $this->em = $entityManager;
        $this->encoder = $encoder;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param array $data
     * @return User
     * @throws Exception
     */
    public function authenticateUser(array $data): User
    {
        /** @var User $user */
        $user = $this->em->getRepository(User::class)->findOneUserByEmailOrUsername(
            $data['login']
        );

        if (
            !$user ||
            !$user->isEnabled() ||
            !$this->encoder->isPasswordValid($user, $data['password'])
        ) {
            throw new Exception('invalid credentials', Response::HTTP_FORBIDDEN);
        }

        return $user;
    }
}
