<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class LoadUserData extends Fixture implements ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $path = $this->container->getParameter('kernel.root_dir') . '/Resources/fixtures/users.csv';
        $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
        $user_array = $serializer->decode(file_get_contents($path), 'csv');

        foreach ($user_array as $item) {
            $user = new User();
            $password = $this->container->get('security.password_encoder')->encodePassword($user, $item['password']);
            $user->setEmail($item['email'])
                ->setEnabled(true)
                ->setUsername($item['username'])
                ->setPassword($password)
                ->setRoles([$item['role']]);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
