<?php

namespace App\Controller;

use App\Controller\Common\FormErrorHandlerTrait;
use App\Entity\Product\Product;
use App\Form\ProductType;
use App\Service\ProductService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;
use Symfony\Component\Serializer\SerializerInterface;

class AdminProductController
{
    use FormErrorHandlerTrait;

    /**
     * @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     type="string",
     *     required=true,
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="barcode", type="string", example="1234567"),
     *         @SWG\Property(property="name", type="string", example="product1"),
     *         @SWG\Property(property="cost", type="float", example="9.99"),
     *         @SWG\Property(
     *              property="vat_class_type",
     *              type="integer",
     *              example="1",
     *              description="1 - 6%, 2 - 21%"),
     *     )
     * )
     *
     * @SWG\Response(
     *     response=201,
     *     description="Product created",
     *     @SWG\Schema(
     *        type="object",
     *        @SWG\Property(property="id", type="string", example="1234567"),
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
     *        response=400,
     *        description="Bad request",
     *        @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="code", type="integer", enum={400}),
     *            @SWG\Property(property="message", type="string", example="Bad Request")
     *        )
     * )
     * @SWG\Response(
     *        response=403,
     *        description="Access denied",
     *        @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="code", type="integer", enum={403}),
     *            @SWG\Property(property="message", type="string", example="Access denied")
     *        )
     * )
     * @SWG\Tag(name="Admin product")
     *
     * @Route("/api/admin/product", name="admin_product_create", methods="POST")
     * @return  JsonResponse
     */
    public function createProduct(
        Request $request,
        FormFactoryInterface $formFactory,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager
    ) {
        $product = new Product();
        $form = $formFactory->create(ProductType::class, $product);
        $form->submit($request->request->all());
        if ($form->isValid()) {
            $entityManager->persist($product);
            $entityManager->flush();
            $entityManager->refresh($product);

            return new JsonResponse(
                $serializer->serialize($product, 'json', ['groups' => 'full']),
                Response::HTTP_CREATED
            );
        } else {
            return $this->getFormErrorsResponse($form);
        }
    }

    /**
     * @SWG\Response(
     *     response=200,
     *     description="Returns the list of products",
     *     @SWG\Schema(
     *       type="object",
     *       @SWG\Property(property="products", type="array", description="List of products", @SWG\Items(
     *                 type="object",
     *                 @SWG\Property(property="id", type="string", example="1234567"),
     *                 @SWG\Property(property="barcode", type="string", example="1234567"),
     *                 @SWG\Property(property="name", type="string", example="product1"),
     *                 @SWG\Property(property="cost", type="float", enum={9.99}),
     *                 @SWG\Property(
     *                      property="vat_class_type",
     *                      type="integer",
     *                      example="1",
     *                      description="1 - 6%, 2 - 21%"),
     *                 )
     *           ),
     *       @SWG\Property(property="cursor", type="object",
     *                 @SWG\Property(property="count", type="integer"),
     *       ),
     *     )
     * )
     * @SWG\Parameter(
     *         name="limit",
     *         in="query",
     *         type="integer",
     *         description="Limit for pagination"
     *     )
     * @SWG\Parameter(
     *         name="offset",
     *         in="query",
     *         type="integer",
     *         description="Cursor position"
     *     )
     * @SWG\Tag(name="Admin product")
     * @Route("/api/admin/product/list", name="admin_get_products_list", methods="GET")
     */
    public function getProductList(
        ProductService $productService,
        Request $request,
        SerializerInterface $serializer
    ) {
        $data = $productService->getProductList(
            $request->query->getInt('limit', 0),
            $request->query->getInt('offset', 1)
        );

        return new JsonResponse(
            $serializer->serialize($data, 'json', ['groups' => ['full']])
        );
    }
}
