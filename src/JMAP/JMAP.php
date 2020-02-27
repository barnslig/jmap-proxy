<?php

namespace JP\JMAP;

use JP\JMAP\Exceptions\UnknownCapabilityException;
use JP\JMAP\RequestErrors\LimitError;
use JP\JMAP\RequestErrors\NotJsonError;
use JP\JMAP\RequestErrors\NotRequestError;
use JP\JMAP\RequestErrors\UnknownCapabilityError;
use JsonSchema\Exception\ValidationException;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * A JMAP server
 *
 * This class automatically instantiates a session and the core capability.
 * Make sure to attach the PSR-15 compatible HTTP handlers!
 */
class JMAP
{
    /** @var array */
    private $options;

    /** @var Session */
    private $session;

    /**
     * Construct a new JMAP server instance
     *
     * @param array $options Global server options
     */
    public function __construct(array $options = [])
    {
        $this->options = array_merge([
            "maxSizeUpload" => 50000000,
            "maxConcurrentUpload" => 4,
            "maxSizeRequest" => 10000000,
            "maxConcurrentRequests" => 4,
            "maxCallsInRequest" => 16,
            "maxObjectsInGet" => 500,
            "maxObjectsInSet" => 500,
            "collationAlgorithms" => []
        ], $options);

        $this->session = new Session();
        $this->session->addCapability(new Capabilities\CoreCapability($this->options));
    }

    /**
     * Get an option
     *
     * @param string $key Option key
     * @return mixed
     */
    public function getOption(string $key)
    {
        return $this->options[$key];
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
     * PSR-15 HTTP Session Handler: /.well-known/jmap
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
     * PSR-15 HTTP API Handler: /api
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
        try {
            $parsedBody = json_decode(file_get_contents('php://input'), false, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            $error = new NotJsonError();
            return $error->asResponse();
        }

        // 2. Construct JMAP request
        try {
            $jmapRequest = new Request($parsedBody, $this->getOption("maxCallsInRequest"));
        } catch (ValidationException $exception) {
            $error = new NotRequestError($exception);
            return $error->asResponse();
        } catch (\OverflowException $exception) {
            $error = new LimitError($exception->getMessage(), "maxCallsInRequest");
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
