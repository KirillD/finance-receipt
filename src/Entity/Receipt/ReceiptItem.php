<?php

namespace App\Entity\Receipt;

use App\Entity\Product\Product;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="receipt_item")
 * @ORM\Entity()
 */
class ReceiptItem
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product\Product")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Receipt\Receipt", inversedBy="receiptItems")
     * @ORM\JoinColumn(name="receipt_it", referencedColumnName="id")
     */
    private $receipt;

    /**
     * @ORM\Column(name="amount", type="integer")
     */
    private $amount;

    public function __construct()
    {
        $this->amount = 1;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Product
     */
    public function getProduct(): ?Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     * @return ReceiptItem
     */
    public function setProduct(?Product $product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return Receipt
     */
    public function getReceipt(): ?Receipt
    {
        return $this->receipt;
    }

    /**
     * @param Receipt $receipt
     * @return ReceiptItem
     */
    public function setReceipt(?Receipt $receipt)
    {
        $this->receipt = $receipt;

        return $this;
    }

    /**
     * @return int
     */
    public function getAmount(): ?int
    {
        return $this->amount;
    }

    /**
     * @param int $amount
     * @return ReceiptItem
     */
    public function setAmount(?int $amount)
    {
        $this->amount = $amount;

        return $this;
    }
}
