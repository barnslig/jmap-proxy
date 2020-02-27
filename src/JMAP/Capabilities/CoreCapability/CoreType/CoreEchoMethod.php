<?php

namespace JP\JMAP\Capabilities\CoreCapability\CoreType;

use JP\JMAP\Invocation;
use JP\JMAP\Method;
use JP\JMAP\Session;

class CoreEchoMethod implements Method
{
    public function getName(): string
    {
        return "echo";
    }

    public function handle(Invocation $request, Session $session): Invocation
    {
        return $request;
    }
}
