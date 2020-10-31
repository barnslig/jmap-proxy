<?php

namespace barnslig\JMAP\Core;

use barnslig\JMAP\Core\RequestErrors\NotJsonError;
use Ds\Map;
use Ds\Vector;
use OverflowException;
use Psr\Http\Message\ServerRequestInterface;

/**
 * JMAP request
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
     * @param array<string> $using Set of capabilities the client wishes to use
     * @param array<array> $methodCalls Method calls to be processed
     * @param array<string, string> $createdIds Optional. A map of a (client-specified) creation id to the id the
     *                                          server assigned when a record was successfully created
     */
    public function __construct(array $using, array $methodCalls, array $createdIds = [])
    {
        // 1.1. Set used capabilities
        $this->using = new Vector($using);

        // 1.2. Sort the capability identifiers to canonicalize them for e.g. caching
        $this->using->sort();

        // 2. Turn method calls into Invocation instances
        $this->methodCalls = (new Vector($methodCalls))->map(function ($methodCall) {
            return new Invocation($methodCall[0], (array) $methodCall[1], $methodCall[2]);
        });

        // 3. Set created IDs
        $this->createdIds = new Map($createdIds);
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
