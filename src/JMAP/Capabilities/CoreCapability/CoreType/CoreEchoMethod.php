<?php

namespace JP\JMAP\Capabilities\CoreCapability\CoreType;

use JP\JMAP\Invocation;
use JP\JMAP\Method;
use JP\JMAP\Session;

class CoreEchoMethod extends Method
{
    public function handle(Invocation $request, Session $session): Invocation
    {
        return $request;
    }
}
