<?php

namespace JP\JMAP;

/**
 * Abstract class to implement a JMAP method
 *
 * Methods are what is actually called during a Request. They process the input
 * given as an Invocation and return the Invocation where arguments are
 * replaced by the return values.
 *
 * Each Method is attached to a Type.
 */
abstract class Method
{
    abstract public function handle(Invocation $request, Session $session): Invocation;
}
