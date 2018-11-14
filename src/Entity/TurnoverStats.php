<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="turnover_stats")
 * @ORM\Entity(repositoryClass="App\Repository\TurnoverRepository")
 */
class TurnoverStats
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"full"})
     */
    private $id;

    /**
     * @ORM\Column(name="turnover", type="decimal", scale=0)
     */
    protected $turnover;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getTurnover()
    {
        return $this->turnover;
    }

    /**
     * @param mixed $turnover
     * @return TurnoverStats
     */
    public function setTurnover($turnover)
    {
        $this->turnover = $turnover;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     * @return TurnoverStats
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
