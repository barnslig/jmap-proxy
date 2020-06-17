<?php

namespace barnslig\JMAP\Core;

use Ds\Map;

/**
 * Interface to implement a JMAP type
 *
 * Types define an interface for creating, retrieving, updating, and deleting
 * objects of their particular type. For a Foo data type, records of that type
 * would be fetched via a Foo/get call and modified via a Foo/set call.
 * Delta updates may be fetched via a Foo/changes call. These methods all
 * follow a standard format as described below. Some types may not have all
 * these methods.
 */
interface Type
{
    /**
     * Get the type name
     *
     * @return string Type name, e.g. Core
     */
    public function getName(): string;
}
