<?php

namespace Barnslig\Jmap\Core\Capabilities\CoreCapability\CoreType;

use Barnslig\Jmap\Core\Invocation;
use Barnslig\Jmap\Core\Method;
use Barnslig\Jmap\Core\RequestContext;

class CoreEchoMethod implements Method
{
    public function handle(Invocation $request, RequestContext $context): Invocation
    {
        return $request;
    }
}
