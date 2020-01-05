<?php

namespace JP\JMAP;

use Ds\Map;
use JP\JMAP\Exceptions\UnknownCapabilityException;
use JP\JMAP\RequestErrors\NotJsonError;
use JP\JMAP\RequestErrors\NotRequestError;
use JP\JMAP\RequestErrors\UnknownCapabilityError;
use JsonSchema\Exception\ValidationException;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class JMAP
{
    /** @var Session */
    private $session;

    public function __construct()
    {
        $this->session = new Session();
    }

    /**
     * Get the current JMAP session
     *
     * @return Session
     */
    public function getSession(): Session
    {
        return $this->session;
    }

    /**
     * HTTP Session Handler: /.well-known/jmap
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function sessionHandler(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse($this->getSession(), 200, [
            'Cache-Control' => ['no-cache, no-store, must-revalidate']
        ]);
    }

    /**
     * HTTP API Handler: /api
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function apiHandler(ServerRequestInterface $request): ResponseInterface
    {
        // 1. Parse request body as JSON
        if ($request->getHeaderLine("Content-Type") !== "application/json") {
            $error = new NotJsonError();
            return $error->asResponse();
        }
        $parsedBody = json_decode(file_get_contents('php://input'));
        if (json_last_error() !== JSON_ERROR_NONE) {
            $error = new NotJsonError();
            return $error->asResponse();
        }

        // 2. Construct JMAP request
        try {
            $jmapRequest = new Request($parsedBody);
        } catch (ValidationException $exception) {
            $error = new NotRequestError($exception);
            return $error->asResponse();
        }

        // 3. Dispatch JMAP request to get JMAP response
        try {
            $response = $this->getSession()->dispatch($jmapRequest);
        } catch (UnknownCapabilityException $exception) {
            $error = new UnknownCapabilityError($exception);
            return $error->asResponse();
        }

        return new JsonResponse($response);
    }
}
