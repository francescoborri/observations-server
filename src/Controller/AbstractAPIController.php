<?php

namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Response;

class AbstractAPIController extends AbstractFOSRestController
{
    public function defaultView($data, int $statusCode, ?string $message = null, bool $noContent = false)
    {
        if (!$data && !$noContent)
            throw $this->createNotFoundException(Response::$statusTexts[Response::HTTP_NOT_FOUND]);

        return $this->view([
            'code' => $statusCode,
            'message' => $message ? $message : Response::$statusTexts[$statusCode],
            'data' => is_array($data) ? $data : [ $data ]
        ], $statusCode);
    }
}
