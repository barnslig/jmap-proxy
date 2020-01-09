<?php

namespace JP\JMAP;

use Ds\Map;
use JP\JMAP\Exceptions\UnknownCapabilityException;
use JsonSerializable;
use OutOfBoundsException;

/**
 * Class implementing a JMAP Session consisting of capabilities, accounts and endpoints
 */
class Session implements JsonSerializable
{
    /** @var Map */
    private $capabilities;

    /** @var Map */
    private $accounts;

    public function __construct()
    {
        $this->capabilities = new Map();
        $this->accounts = new Map();
    }

    /**
     * Add a capability to the JMAP server
     *
     * @param string $key Capability key, usually prefixed with `urn:ietf:params:jmap:`
     * @param Capability $capability Instance of the corresponding capability class
     * @return void
     */
    public function addCapability(string $key, Capability $capability): void
    {
        $this->capabilities->put($key, $capability);
    }

    /**
     * Get the session's hash that the client uses to determine change
     * @return string
     */
    public function getState(): string
    {
        // TODO cache ?
        return mb_substr(sha1(serialize($this->jsonSerialize(false))), 0, 12);
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
        // TODO cache or some other kind of efficiency increase ?
        $methods = new Map();
        foreach ($request->getUsedCapabilities() as $capabilityKey) {
            if (!$this->capabilities->hasKey($capabilityKey)) {
                throw new UnknownCapabilityException("The Request object used capability '" . $capabilityKey . "', which is not supported by this server.");
            }

            $capability = $this->capabilities->get($capabilityKey);
            $methods->putAll($capability->getMethods());
        }

        // 2. For each methodCall, execute the corresponding method, then add it to the response
        $response = new Response($this);
        foreach ($request->getMethodCalls() as $methodCall) {
            // 2.1. Resolve references to previous method results (See RFC 8620 Section  3.7)
            try {
                $methodCall->resolveResultReferences($response->getMethodResponses());
            } catch (\RuntimeException $exception) {
                // TODO
            }

            // 2.2. Execute the corresponding method
            try {
                $methodCallable = $methods->get($methodCall->getName());
                $methodResponse = $methodCallable($methodCall, $this);
            } catch (OutOfBoundsException $exception) {
                $methodResponse = $methodCall->withName("error")->withArguments((object)["type" => "unknownMethod"]);
            }
            $response->addMethodResponse($methodResponse);
        }

        return $response;
    }

    /**
     * Data used to serialize the Session into JSON
     * @param bool $withState Whether the session's state hash should be included
     * @return object
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
