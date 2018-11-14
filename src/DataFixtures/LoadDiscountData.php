<?php

namespace App\DataFixtures;

use App\Entity\Discount;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadDiscountData extends Fixture implements ContainerAwareInterface
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
        $discount = (new Discount())
            ->setStrategy(Discount::STRATEGY_EVERY_THIRD)
            ->setStatus(Discount::STATUS_INACTIVE);

        $manager->persist($discount);

        $manager->flush();
    }
}
