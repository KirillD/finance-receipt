<?php

namespace App\Controller;

use App\Controller\Common\FormErrorHandlerTrait;
use App\Form\LoginType;
use App\Service\SecurityService;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;
use Exception;

class SecurityController
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
     *         @SWG\Property(property="login", type="string", example="mail@mail.com"),
     *         @SWG\Property(property="password", type="string", example="password"),
     *     )
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Receive user access token",
     *     @SWG\Schema(
     *        type="object",
     *        @SWG\Property(property="token", type="string")
     *    )
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
     * @SWG\Tag(name="Security")
     *
     * @Route("/api/login", name="login", methods="POST")
     * @return  JsonResponse
     */
    public function login(
        Request $request,
        JWTTokenManagerInterface $jwtManager,
        FormFactoryInterface $formFactory,
        SecurityService $securityService
    ) {
        $form = $formFactory->create(LoginType::class);
        $form->submit($request->request->all());
        if ($form->isValid()) {
            try {
                $user = $securityService->authenticateUser($form->getData());
            } catch (Exception $exception) {
                return new JsonResponse($exception->getMessage(), $exception->getCode());
            }

            return new JsonResponse(['token' => $jwtManager->create($user)]);
        } else {
            return $this->getFormErrorsResponse($form);
        }
    }
}
