<?php

namespace JP\JMAP;

use Ds\Map;
use JsonSerializable;

/**
 * Abstract class to implement a JMAP capability
 *
 * Capabilities are used to extend the functionality of a JMAP server.
 * It consists of Types that provide Methods. For example, a capability
 * of type "urn:ietf:params:jmap:mail" may have a type called "Mailbox" with
 * a corresponding method called "get".
 *
 * Each capability is attached to a Session.
 */
abstract class Capability implements JsonSerializable
{
    /** @var Map */
    private $types;

    /** @var string */
    private $typesHash;

    public function __construct()
    {
        $this->types = new Map();
    }

    /**
     * Get further information about this capability
     *
     * The return value is used within the JMAP Session Resource to give
     * further information about the server's capabilities in relation to
     * this capability.
     *
     * @see https://tools.ietf.org/html/rfc8620#section-2
     * @return object
     */
    abstract public function getCapabilities(): object;

    /**
     * Add a type consisting of methods
     *
     * @param  string $key Type key, e.g. Core
     * @param  Type $type JMAP Type instance
     * @return void
     */
    final public function addType(string $key, Type $type): void
    {
        $this->types->put($key, $type);
    }

    /**
     * Get Map of all methods provided by this capability
     *
     * @return Map
     */
    final public function getMethods(): Map
    {
        $methods = new Map();
        foreach ($this->types as $typeKey => $type) {
            foreach ($type->getMethods() as $methodKey => $methodCallable) {
                $methods->put($typeKey . "/" . $methodKey, $methodCallable);
            }
        }
        return $methods;
    }

    public function jsonSerialize()
    {
        return $this->getCapabilities();
    }
}
