<?php

namespace App\Controller\Common;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait FormErrorHandlerTrait
{
    /**
     * @param FormInterface $form
     * @return JsonResponse
     */
    private function getFormErrorsResponse(FormInterface $form): JsonResponse
    {
        $errors = [];
        if ($form->getErrors() && (string) $form->getErrors() != null) {
            $errors['form'] = (string) $form->getErrors();
        }
        foreach ($form->all() as $childForm) {
            if (!$childForm->isValid()) {
                $errors[$childForm->getName()] = (string)$childForm->getErrors(true);
            }
        }

        return new JsonResponse($errors, Response::HTTP_BAD_REQUEST);
    }
}
