<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="discount")
 * @ORM\Entity()
 */
class Discount
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    const STRATEGY_EVERY_THIRD = 1;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"full"})
     */
    private $id;

    /**
     * @ORM\Column(name="strategy", type="smallint")
     * @Groups({"full"})
     */
    private $strategy;

    /**
     * @ORM\Column(name="status", type="smallint")
     * @Groups({"full"})
     */
    private $status;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int|null
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @param int|null $status
     * @return Discount
     */
    public function setStatus(?int $status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getStrategy(): ?int
    {
        return $this->strategy;
    }

    /**
     * @param int|null $strategy
     * @return Discount
     */
    public function setStrategy(?int $strategy)
    {
        $this->strategy = $strategy;

        return $this;
    }
}
