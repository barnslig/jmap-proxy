<?php

namespace JP\JMAP;

use Ds\Map;
use JsonSerializable;

/**
 * Abstract class to implement a JMAP capability
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
     * @return object
     */
    abstract public function getCapabilities(): array;

    /**
     * Add a type consisting of methods
     *
     * @param  string $key Type key, e.g. Core
     * @param  Type $type JMAP Type instance
     * @return void
     */
    public function addType(string $key, Type $type): void
    {
        $this->types->put($key, $type);
    }

    /**
     * Get Map of all methods provided by this capability
     *
     * @return Map
     */
    public function getMethods(): Map
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
