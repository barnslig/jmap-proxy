<?php

namespace barnslig\JMAP\Core;

use Ds\Map;
use Ds\Vector;
use barnslig\JMAP\Core\Exceptions\MethodInvocationException;
use barnslig\JMAP\Core\Exceptions\UnknownCapabilityException;
use JsonSerializable;
use OutOfBoundsException;

/**
 * Session consisting of capabilities, accounts and endpoints
 *
 * @see https://tools.ietf.org/html/rfc8620#section-2
 */
class Session implements JsonSerializable
{
    /** @var Map<string, Capability> */
    private $capabilities;

    /** @var Map<string, object> */
    private $accounts;

    public function __construct()
    {
        $this->capabilities = new Map();
        $this->accounts = new Map();
    }

    /**
     * Add a capability to the JMAP server
     *
     * @param Capability $capability Instance of the corresponding capability class
     * @return void
     */
    public function addCapability(Capability $capability): void
    {
        $this->capabilities->put($capability->getName(), $capability);
    }

    /**
     * Get the session's hash that the client uses to determine change
     *
     * @return string
     */
    public function getState(): string
    {
        // TODO cache ?
        return mb_substr(sha1(serialize($this->jsonSerialize(false))), 0, 12);
    }

    /**
     * Resolves a list of capabilities to a map of methods
     *
     * @param Vector<string> $usedCapabilities Vector of capabilities
     * @return Map<string, Method> The keys are full method names (e.g. "Email/get")
     * @throws UnknownCapabilityException When an unknown capability is used
     */
    public function resolveMethods(Vector $usedCapabilities): Map
    {
        // TODO cache or some other kind of efficiency increase ?
        $methods = new Map();
        foreach ($usedCapabilities as $capabilityKey) {
            if (!$this->capabilities->hasKey($capabilityKey)) {
                throw new UnknownCapabilityException(
                    "The Request object used capability '" . $capabilityKey . "', which is not supported by this server"
                );
            }

            $capability = $this->capabilities->get($capabilityKey);
            $methods->putAll($capability->getMethods());
        }
        return $methods;
    }

    /**
     * Resolves a method call into a method response
     *
     * @param Invocation $methodCall - Client-provided method call
     * @param Vector<Invocation> $methodResponses - Vector of already processed method calls
     * @param Map<string, Method> $methods - Map of available methods within this request
     * @return Invocation The method response
     */
    public function resolveMethodCall(Invocation $methodCall, Vector $methodResponses, Map $methods): Invocation
    {
        try {
            // 1. Resolve references to previous method results (See RFC 8620 Section  3.7)
            $methodCall->resolveResultReferences($methodResponses);

            // 2. Execute the corresponding method
            $method = $methods->get($methodCall->getName());
            $methodCallable = new $method();
            $methodResponse = $methodCallable->handle($methodCall, $this);
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
        $methods = $this->resolveMethods($request->getUsedCapabilities());

        // 2. For each methodCall, execute the corresponding method, then add it to the response
        $methodResponses = new Vector();
        foreach ($request->getMethodCalls() as $methodCall) {
            $methodResponse = $this->resolveMethodCall($methodCall, $methodResponses, $methods);
            $methodResponses->push($methodResponse);
        }

        return new Response($this, $methodResponses);
    }

    /**
     * Data used to serialize the Session into JSON
     *
     * @param bool $withState Whether the session's state hash should be included
     * @return array<string, mixed>
     */
    public function jsonSerialize(bool $withState = true)
    {
        $state = $withState ? $this->getState() : null;
        return [
            "capabilities" => $this->capabilities,
            "accounts" => $this->accounts,
            "primaryAccounts" => [],
            "username" => null,
            "apiUrl" => null,
            "downloadUrl" => null,
            "uploadUrl" => null,
            "eventSourceUrl" => null,
            "state" => $state
        ];
    }
}
