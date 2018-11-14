<?php

namespace App\Entity\Receipt;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="receipt")
 * @ORM\Entity(repositoryClass="App\Repository\ReceiptRepository")
 */
class Receipt
{
    const STATUS_NEW = 0;
    const STATUS_FINISHED = 1;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"full", "short"})
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Receipt\ReceiptItem", mappedBy="receipt", cascade={"persist"})
     * @Groups({"full"})
     */
    private $receiptItems;

    /**
     * @ORM\Column(name="status", type="smallint")
     */
    private $status;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    public function __construct()
    {
        $this->createdAt = new DateTime;
        $this->status = self::STATUS_NEW;
        $this->receiptItems = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return ArrayCollection
     */
    public function getReceiptItems()
    {
        return $this->receiptItems;
    }

    public function addReceiptItem(ReceiptItem $receiptItem)
    {
        if (!$this->receiptItems->contains($receiptItem)) {
            $this->receiptItems->add($receiptItem);
            $receiptItem->setReceipt($this);
        }
    }

    /**
     * @param ReceiptItem $receiptItem
     */
    public function removeReceiptItem(ReceiptItem $receiptItem)
    {
        if ($this->receiptItems->contains($receiptItem)) {
            $this->receiptItems->add($receiptItem);
            $receiptItem->setReceipt(null);
        }
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
     * @return Receipt
     */
    public function setStatus(?int $status)
    {
        $this->status = $status;

        return $this;
    }
}
