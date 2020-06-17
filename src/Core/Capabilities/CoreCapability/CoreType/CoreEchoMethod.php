<?php

namespace barnslig\JMAP\Core\Capabilities\CoreCapability\CoreType;

use barnslig\JMAP\Core\Invocation;
use barnslig\JMAP\Core\Method;
use barnslig\JMAP\Core\Session;

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
