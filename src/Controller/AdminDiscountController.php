<?php

namespace App\Controller;

use App\Entity\Discount;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;
use Symfony\Component\Serializer\SerializerInterface;

class AdminDiscountController
{
    /**
     * @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     type="string",
     *     required=true,
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="status", type="string", example="1", description="0 - inactive, 1 - active"),
     *     )
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Discount status change",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="id", type="string", example="1234567"),
     *         @SWG\Property(property="strategy", type="string", example="1", description="1 - every third free"),
     *         @SWG\Property(property="status", type="string", example="1"),
     *    )
     * )
     * @SWG\Response(
     *        response=400,
     *        description="Bad request",
     *        @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="code", type="integer", enum={404}),
     *            @SWG\Property(property="message", type="string", example="Bad request")
     *        )
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
     * @SWG\Tag(name="Admin discount")
     *
     * @Route("/api/admin/discount/change-status/{id}", name="admin_discount_status_change", methods="PUT")
     * @return  JsonResponse
     */
    public function createProduct(
        int $id,
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager
    ) {
        $discount = $entityManager->getRepository(Discount::class)->find($id);
        if (!$discount) {
            return new JsonResponse('Not found', Response::HTTP_NOT_FOUND);
        }
        $data = $request->request->all();
        if (
            !isset($data['status']) ||
            !in_array($data['status'], [Discount::STATUS_INACTIVE, Discount::STATUS_ACTIVE])
        ) {
            return new JsonResponse('Bad request', Response::HTTP_BAD_REQUEST);
        }

        $discount->setStatus($data['status']);
        $entityManager->flush();

        return new JsonResponse(
            $serializer->serialize($discount, 'json', ['groups' => 'full']),
            Response::HTTP_CREATED
        );

    }

    /**
     * @SWG\Response(
     *     response=200,
     *     description="Returns the list of products",
     *     @SWG\Schema(
     *       type="object",
     *       @SWG\Property(property="discounts", type="array", description="List of discounts", @SWG\Items(
     *                 type="object",
     *                 @SWG\Property(property="id", type="string", example="1234567"),
     *                 @SWG\Property(property="strategy", type="string", example="1", description="1 - every third free"),
     *                 @SWG\Property(property="status", type="string", example="1"),
     *           )),
     *     )
     * )
     * @SWG\Tag(name="Admin discount")
     * @Route("/api/admin/discount/list", name="admin_get_discounts_list", methods="GET")
     */
    public function getDiscountList(
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager
    ) {
        return new JsonResponse(
            $serializer->serialize(
                $entityManager->getRepository(Discount::class)->findAll(),
                'json',
                ['groups' => ['full']]
            )
        );
    }
}
