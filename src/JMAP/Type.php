<?php

namespace JP\JMAP;

use Ds\Map;

/**
 * Abstract class to implement a JMAP type
 *
 * Types define an interface for creating, retrieving, updating, and deleting
 * objects of their particular type. For a Foo data type, records of that type
 * would be fetched via a Foo/get call and modified via a Foo/set call.
 * Delta updates may be fetched via a Foo/changes call. These methods all
 * follow a standard format as described below. Some types may not have all
 * these methods.
 *
 * Each type is attached to a Capability.
 */
abstract class Type
{
    /** @var Map */
    private $methods;

    public function __construct()
    {
        $this->methods = new Map();
    }

    /**
     * Add a method to the type
     *
     * @param string $method Method key, e.g. "get"
     * @param Method $fn Handler class
     */
    public function addMethod(string $method, Method $fn): void
    {
        $this->methods->put($method, $fn);
    }

    /**
     * Get all type methods
     *
     * @return Map
     */
    public function getMethods(): Map
    {
        return $this->methods;
    }
}
