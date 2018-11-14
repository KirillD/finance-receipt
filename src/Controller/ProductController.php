<?php

namespace App\Controller;

use App\Entity\Product\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;
use Symfony\Component\Serializer\SerializerInterface;

class ProductController
{
    /**
     *
     * @SWG\Response(
     *     response=200,
     *     description="Product",
     *     @SWG\Schema(
     *        type="object",
     *        @SWG\Property(property="barcode", type="string", example="1234567"),
     *        @SWG\Property(property="name", type="string", example="product1"),
     *        @SWG\Property(property="cost", type="float", enum={9.99}),
     *        @SWG\Property(
     *             property="vat_class_type",
     *             type="integer",
     *             example="1",
     *             description="1 - 6%, 2 - 21%"),
     *    ))
     * )
     * @SWG\Response(
     *        response=404,
     *        description="Not found",
     *        @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="code", type="integer", enum={404}),
     *            @SWG\Property(property="message", type="string", example="Not found")
     *        )
     * )
     * @SWG\Tag(name="Product")
     *
     * @Route("/api/product/{barcode}", name="get_product", methods="GET")
     * @return  JsonResponse
     */
    public function getProduct(
        string $barcode,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager
    ) {
        $product = $entityManager->getRepository(Product::class)->findBy(['barcode' => $barcode]);
        if (!$product) {
            return new JsonResponse('Not found', Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(
            $serializer->serialize($product, 'json', ['groups' => 'full']),
            Response::HTTP_CREATED
        );
    }
}
