<?php

namespace App\Service;

use App\Entity\Product\Product;
use Doctrine\ORM\EntityManagerInterface;

class ProductService
{
    const LIMIT = 10;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * ProductService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->em = $entityManager;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getProductList(int $limit, int $offset)
    {
        $limit = $limit ? $limit : self::LIMIT;
        $offset = $offset - 1;
        $productRepo = $this->em->getRepository(Product::class);
        $products = $productRepo->findByLimitAndOffset($limit, $offset);

        return [
            'products' => $products,
            'cursor' => [
                'count' => (int) $productRepo->getProductsCount()
            ]
        ];
    }
}
