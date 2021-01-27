<?php

namespace Barnslig\Jmap\Core\Controllers;

use BadMethodCallException;
use Barnslig\Jmap\Core\Exceptions\MethodInvocationException;
use Barnslig\Jmap\Core\Exceptions\UnknownCapabilityException;
use Barnslig\Jmap\Core\Invocation;
use Barnslig\Jmap\Core\Request;
use Barnslig\Jmap\Core\RequestErrors\LimitError;
use Barnslig\Jmap\Core\RequestErrors\NotJsonError;
use Barnslig\Jmap\Core\RequestErrors\NotRequestError;
use Barnslig\Jmap\Core\RequestErrors\UnknownCapabilityError;
use Barnslig\Jmap\Core\Response;
use Barnslig\Jmap\Core\Schemas\ValidationException;
use Ds\Map;
use Ds\Vector;
use OutOfBoundsException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Controller handling JMAP API requests
 */
class ApiController extends AbstractController
{
    /**
     * Parses a JSON request body
     *
     * @param ServerRequestInterface $request Server request
     * @throws \JsonException When decoding the request body to JSON failed
     * @throws \RuntimeException When the Content-Type is not application/json
     * @return object Request body as parsed JSON
     */
    public static function parseJsonBody(ServerRequestInterface $request): object
    {
        if ($request->getHeaderLine("Content-Type") !== "application/json") {
            throw new \RuntimeException("Wrong Content-Type");
        }

        return json_decode($request->getBody(), false, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Resolves a method call into a method response
     *
     * @param Invocation $methodCall - Client-provided method call
     * @param Vector<Invocation> $methodResponses - Vector of already processed method calls
     * @param Map<string, class-string> $methods - Map of available methods within this request
     * @return Invocation The method response
     */
    public function resolveMethodCall(Invocation $methodCall, Vector $methodResponses, Map $methods): Invocation
    {
        try {
            // 1. Resolve references to previous method results (See RFC 8620 Section  3.7)
            $methodCall->resolveResultReferences($methodResponses);

            // 2. Resolve the method
            $method = $methods->get($methodCall->getName());
            if (!class_exists($method)) {
                throw new BadMethodCallException("Could not resolve method " . $method);
            }

            // 3. Execute the method
            $methodCallable = new $method();
            $method::validate($methodCall, $this->getContext());
            $methodResponse = $methodCallable->handle($methodCall, $this->getContext());
        } catch (OutOfBoundsException $exception) {
            $methodResponse = $methodCall->withName("error")->withArguments(["type" => "unknownMethod"]);
        } catch (MethodInvocationException $exception) {
            $args = ["type" => $exception->getType()];
            if ($exception->getMessage()) {
                $args["description"] = $exception->getMessage();
            }
            $methodResponse = $methodCall->withName("error")->withArguments($args);
        }
        return $methodResponse;
    }

    /**
     * Dispatch a JMAP request and turn it into a JMAP response
     *
     * @param Request $request
     * @return Response
     */
    public function dispatch(Request $request): Response
    {
        // 1. Build map with all supported methods of the used capabilities
        $methods = $this->getContext()->getSession()->resolveMethods($request->getUsedCapabilities());

        // 2. For each methodCall, execute the corresponding method, then add it to the response
        $methodResponses = new Vector();
        foreach ($request->getMethodCalls() as $methodCall) {
            $methodResponse = $this->resolveMethodCall($methodCall, $methodResponses, $methods);
            $methodResponses->push($methodResponse);
        }

        return new Response($this->getContext()->getSession(), $methodResponses);
    }

    /**
     * Process a JMAP API request
     *
     * @param ServerRequestInterface $request Server request
     * @return ResponseInterface HTTP response
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // 1. Turn HTTP request into a JSON object
        // TODO Use PSR-7 middlewares for content negotiation and body parsing
        try {
            /** @var mixed */
            $data = self::parseJsonBody($request);
        } catch (\Exception $exception) {
            return new NotJsonError();
        }

        // 2. Ensure that the request data adheres the schema
        try {
            $this->getContext()->getValidator()->validate($data, "http://jmap.io/Request.json#");
        } catch (ValidationException $exception) {
            return new NotRequestError($exception);
        }

        // 3. Turn the HTTP request into a JMAP request
        $jmapRequest = new Request($data->using, $data->methodCalls, $data->createdIds ?? []);

        // 4. Make sure that the amount of requested method calls does not exceed the limit
        if ($jmapRequest->getMethodCalls()->count() > $this->getOption("maxCallsInRequest")) {
            return new LimitError(
                "The maximum number of " . $this->getOption("maxCallsInRequest") . " method calls got exceeded.",
                "maxCallsInRequest"
            );
        }

        // 5. Dispatch the JMAP request
        try {
            return $this->dispatch($jmapRequest);
        } catch (UnknownCapabilityException $exception) {
            return new UnknownCapabilityError($exception);
        }
    }
}
