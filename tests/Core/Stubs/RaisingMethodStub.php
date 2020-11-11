<?php

namespace Barnslig\Jmap\Tests\Core\Stubs;

use Barnslig\Jmap\Core\Exceptions\MethodInvocationException;
use Barnslig\Jmap\Core\Invocation;
use Barnslig\Jmap\Core\Method;
use Barnslig\Jmap\Core\RequestContext;

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
