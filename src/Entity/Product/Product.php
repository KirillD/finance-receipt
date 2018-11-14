<?php

namespace App\Entity\Product;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="product")
 * @UniqueEntity("barcode")
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 */
class Product implements VatClassInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="barcode",type="string", length=32, unique=true)
     * @Groups({"full"})
     */
    protected $barcode;

    /**
     * @ORM\Column(name="name",type="string", length=32)
     * @Groups({"full"})
     */
    protected $name;

    /**
     * @ORM\Column(name="cost", type="decimal", scale=0)
     * @Groups({"full"})
     */
    protected $cost;

    /**
     * @ORM\Column(name="vat_class", type="smallint")
     * @Groups({"full"})
     */
    protected $vatClassType;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    public function __construct()
    {
        $this->createdAt = new DateTime;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getBarcode(): ?string
    {
        return $this->barcode;
    }

    /**
     * @param null|string $barcode
     * @return Product
     */
    public function setBarcode(?string $barcode)
    {
        $this->barcode = $barcode;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return Product
     */
    public function setName(?string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getCost(): ?float
    {
        return $this->cost;
    }

    /**
     * @param float|null $cost
     * @return Product
     */
    public function setCost(?float $cost)
    {
        $this->cost = $cost;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getVatClassType(): ?int
    {
        return $this->vatClassType;
    }

    /**
     * @param int|null $vatClassType
     * @return Product
     */
    public function setVatClassType(?int $vatClassType)
    {
        $this->vatClassType = $vatClassType;

        return $this;
    }
}
