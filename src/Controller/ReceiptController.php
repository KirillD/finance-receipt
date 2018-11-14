<?php

namespace App\Controller;

use App\Controller\Common\FormErrorHandlerTrait;
use App\Entity\Receipt\Receipt;
use App\Form\ReceiptType;
use App\Service\ReceiptService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;
use Symfony\Component\Serializer\SerializerInterface;

class ReceiptController
{
    use FormErrorHandlerTrait;

    /**
     *
     * @SWG\Response(
     *     response=201,
     *     description="Receipt",
     *     @SWG\Schema(
     *        type="object",
     *        @SWG\Property(property="id", type="string", example="1")
     *      )
     * )
     * @SWG\Tag(name="Receipt")
     *
     * @Route("/api/receipt", name="create_receipt", methods="POST")
     * @return JsonResponse
     */
    public function createReceipt(
        SerializerInterface $serializer,
        ReceiptService $receiptService
    ) {
        $receipt = $receiptService->createReceipt();

        return new JsonResponse(
            $serializer->serialize($receipt, 'json', ['groups' => 'short']),
            Response::HTTP_CREATED
        );
    }

    /**
     * @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     type="string",
     *     required=true,
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="barcode", type="string", example="1234567"),
     *         @SWG\Property(property="amoun", type="integer", enum={1}),
     *     )
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Receipt",
     *     @SWG\Schema(
     *        type="object",
     *        @SWG\Property(property="id", type="string", example="1")
     *      )
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
     *        response=404,
     *        description="Not found",
     *        @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="code", type="integer", enum={404}),
     *            @SWG\Property(property="message", type="string", example="Not found")
     *        )
     * )
     * @SWG\Tag(name="Receipt")
     *
     * @Route("/api/receipt/{id}", name="update_receipt", methods="PUT")
     * @return JsonResponse
     */
    public function updateReceipt(
        int $id,
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory,
        ReceiptService $receiptService
    ) {
        $receipt = $entityManager->getRepository(Receipt::class)->find($id);
        if (!$receipt || $receipt->getStatus() == Receipt::STATUS_FINISHED) {
            return new JsonResponse('Not found', Response::HTTP_NOT_FOUND);
        }
        $form = $formFactory->create(ReceiptType::class);
        $form->submit($request->request->all());
        if ($form->isValid()) {
            try {
                $receiptService->updateReceipt($receipt, $form->getData());

                return new JsonResponse(
                    $serializer->serialize($receipt, 'json', ['groups' => 'full'])
                );
            } catch (Exception $exception) {
                return new JsonResponse($exception->getMessage(), $exception->getCode());
            }
        } else {
            return $this->getFormErrorsResponse($form);
        }
    }

    /**
     *
     * @SWG\Response(
     *     response=200,
     *     description="Receipt",
     *     @SWG\Schema(
     *        type="object",
     *        @SWG\Property(property="id", type="string", example="1")
     *      )
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
     * @SWG\Tag(name="Receipt")
     *
     * @Route("/api/receipt/finish/{id}", name="finish_receipt", methods="PUT")
     * @return JsonResponse
     */
    public function finishReceipt(
        int $id,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        ReceiptService $receiptService
    ) {
        $receipt = $entityManager->getRepository(Receipt::class)->find($id);
        if (!$receipt || $receipt->getStatus() == Receipt::STATUS_FINISHED) {
            return new JsonResponse('Not found', Response::HTTP_NOT_FOUND);
        }
        try {
            $receiptService->finishReceipt($receipt);

            return new JsonResponse(
                $serializer->serialize($receipt, 'json', ['groups' => 'full'])
            );
        } catch (Exception $exception) {
            return new JsonResponse($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     *
     * @SWG\Response(
     *     response=200,
     *     description="Receipt",
     *     @SWG\Schema(
     *        type="object",
     *        @SWG\Property(property="id", type="string", example="1"),
     *        @SWG\Property(property="status", type="integer", description="0 - new, 1 - finished"),
     *        @SWG\Property(property="receipt_items", type="array", @SWG\Items(
     *                 type="object",
     *                 @SWG\Property(property="name", type="string"),
     *                 @SWG\Property(property="amount", type="integer"),
     *                 @SWG\Property(property="cost", type="integer"),
     *                 @SWG\Property(property="vat_cost", type="integer"),
     *                 @SWG\Property(property="vat_class", type="integer"),
     *        )),
     *        @SWG\Property(property="total", type="object",
     *                 @SWG\Property(property="vat_6", type="integer"),
     *                 @SWG\Property(property="vat_21", type="integer"),
     *                 @SWG\Property(property="total", type="integer"),
     *       ),
     *      )
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
     *        response=404,
     *        description="Not found",
     *        @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="code", type="integer", enum={404}),
     *            @SWG\Property(property="message", type="string", example="Not found")
     *        )
     * )
     * @SWG\Tag(name="Receipt")
     *
     * @Route("/api/receipt/{id}", name="get_receipt", methods="GET")
     * @return  JsonResponse
     */
    public function getReceipt(
        int $id,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager
    ) {
        $receipt = $entityManager->getRepository(Receipt::class)->find($id);
        if (!$receipt) {
            return new JsonResponse('Not found', Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(
            $serializer->serialize($receipt, 'json', ['groups' => 'full']),
            Response::HTTP_CREATED
        );
    }
}
