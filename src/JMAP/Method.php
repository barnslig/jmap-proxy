<?php

namespace JP\JMAP;

/**
 * Interface to implement a JMAP method
 *
 * Methods are what is actually called during a Request. They process the input
 * given as an Invocation and return the Invocation where arguments are
 * replaced by the return values.
 */
interface Method
{
    /**
     * Get the type name
     *
     * @return string Type name, e.g. Core
     */
    public function getName(): string;

    /**
     * Invoke the method
     *
     * @param Invocation $request Request invocation
     * @param Session $session JMAP session instance
     * @return Invocation Response invocation
     */
    public function handle(Invocation $request, Session $session): Invocation;
}
