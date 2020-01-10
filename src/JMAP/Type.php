<?php

namespace JP\JMAP;

use Ds\Map;
use JP\JMAP\TypeMethodInterface;

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
