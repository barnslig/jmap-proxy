<?php

namespace barnslig\JMAP\Core;

use Ds\Map;
use Ds\Vector;
use barnslig\JMAP\Core\Schemas\Validator;
use OverflowException;

/**
 * JMAP request based on a JSON object that needs to be validated
 *
 * @see https://tools.ietf.org/html/rfc8620#section-3.3
 */
class Request
{
    /**
     * Capability identifiers
     *
     * @var Vector<string>
     */
    private $using;

    /**
     * Method calls
     *
     * @var Vector<Invocation>
     */
    private $methodCalls;

    /**
     * Object ID mappings
     *
     * @var Map<string, string>
     */
    private $createdIds;

    /**
     * Construct a new Request instance
     *
     * During construction the JSON request body gets validated against a
     * JSON schema and method calls are mapped into Invocation instances.
     *
     * @param mixed $data Parsed JSON request body
     * @param int $maxCallsInRequest Maximum calls a request may have
     * @throws OverflowException When maxCallsInRequest gets exceeded
     */
    public function __construct($data, int $maxCallsInRequest)
    {
        $validator = new Validator();
        $validator->validate($data, "http://jmap.io/Request.json#");

        $this->using = new Vector($data->using);
        // Sort the capability identifiers to canonicalize them for e.g. caching
        $this->using->sort();

        if (count($data->methodCalls) > $maxCallsInRequest) {
            throw new OverflowException("The maximum number of " . $maxCallsInRequest . " method calls got exceeded.");
        }

        // Turn method calls into Invocation instances
        $this->methodCalls = (new Vector($data->methodCalls))->map(function ($methodCall) {
            return new Invocation($methodCall[0], (array) $methodCall[1], $methodCall[2]);
        });

        $this->createdIds = new Map($data->createdIds ?? []);
    }

    /**
     * Get used capabilities
     *
     * @return Vector<string> Used capabilities
     */
    public function getUsedCapabilities(): Vector
    {
        return $this->using;
    }

    /**
     * Get method calls
     *
     * @return Vector<Invocation> Method calls
     */
    public function getMethodCalls(): Vector
    {
        return $this->methodCalls;
    }

    /**
     * Get created IDs
     *
     * @return Map<string, string> Created IDs
     */
    public function getCreatedIds(): Map
    {
        return $this->createdIds;
    }
}
