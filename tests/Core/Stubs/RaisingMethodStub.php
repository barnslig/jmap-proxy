<?php

namespace barnslig\JMAP\Tests\Core\Stubs;

use barnslig\JMAP\Core\Exceptions\MethodInvocationException;
use barnslig\JMAP\Core\Invocation;
use barnslig\JMAP\Core\Method;
use barnslig\JMAP\Core\RequestContext;

/**
 * A JMAP method that is always raising a method invocation exception
 */
class RaisingMethodStub implements Method
{
    public function handle(Invocation $request, RequestContext $context): Invocation
    {
        throw new MethodInvocationException("test", "testmessage");
    }
}
