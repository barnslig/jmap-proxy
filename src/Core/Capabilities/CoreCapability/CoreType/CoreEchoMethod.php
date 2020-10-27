<?php

namespace barnslig\JMAP\Core\Capabilities\CoreCapability\CoreType;

use barnslig\JMAP\Core\Invocation;
use barnslig\JMAP\Core\Method;
use barnslig\JMAP\Core\RequestContext;

class CoreEchoMethod implements Method
{
    public function handle(Invocation $request, RequestContext $context): Invocation
    {
        return $request;
    }
}
