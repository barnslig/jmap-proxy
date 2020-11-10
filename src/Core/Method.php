<?php

namespace barnslig\JMAP\Core;

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
     * Invoke the method
     *
     * @param Invocation $request Request invocation
     * @param RequestContext $context JMAP request context
     * @return Invocation Response invocation
     */
    public function handle(Invocation $request, RequestContext $context): Invocation;
}
