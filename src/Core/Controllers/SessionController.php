<?php

namespace barnslig\JMAP\Core\Controllers;

use barnslig\JMAP\Core\Session;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class SessionController extends AbstractController
{
    /**
     * Process a JMAP session request
     *
     * @param ServerRequestInterface $request Server request
     * @return ResponseInterface HTTP response
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse($this->getContext()->getSession(), 200, [
            'Cache-Control' => ['no-cache, no-store, must-revalidate']
        ]);
    }
}
