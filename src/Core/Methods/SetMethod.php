<?php

namespace barnslig\JMAP\Core\Methods;

use barnslig\JMAP\Core\Invocation;
use barnslig\JMAP\Core\Method;
use barnslig\JMAP\Core\Methods\Traits\Validate;
use barnslig\JMAP\Core\Session;

abstract class SetMethod implements Method
{
    use Validate;

    public function getName(): string
    {
        return "set";
    }

    public function handle(Invocation $request, Session $session): Invocation
    {
        return $this->validate($request, "http://jmap.io/methods/set.json#");
    }
}
