<?php

namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Response;

class AbstractAPIController extends AbstractFOSRestController
{
    public function defaultView($data, int $defaultStatusCode, ?string $message = null, ?string $errorMessage = null, bool $noContent = false)
    {
        if (!$data && !$noContent)
            throw $this->createNotFoundException($errorMessage ? $errorMessage : Response::$statusTexts[Response::HTTP_NOT_FOUND]);

        return $this->view([
            'status' => $defaultStatusCode,
            'message' => $message ? $message : Response::$statusTexts[$defaultStatusCode] . '.',
            'data' => is_array($data) ? $data : [ $data ]
        ], $defaultStatusCode);
    }
}
