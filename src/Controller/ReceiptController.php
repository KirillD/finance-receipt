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
        EntityManagerInterface $entityManager
    ) {
        $receipt = new Receipt();
        $entityManager->persist($receipt);
        $entityManager->flush();
        $entityManager->refresh($receipt);

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
}
