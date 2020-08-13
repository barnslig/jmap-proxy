<?php

namespace barnslig\JMAP\Core\Methods;

use barnslig\JMAP\Core\Invocation;
use barnslig\JMAP\Core\Method;
use barnslig\JMAP\Core\Methods\Traits\Validate;
use barnslig\JMAP\Core\Session;

abstract class QueryChangesMethod implements Method
{
    use Validate;

    public function getName(): string
    {
        return "queryChanges";
    }

    public function handle(Invocation $request, Session $session): Invocation
    {
        return $this->validate($request, "http://jmap.io/methods/queryChanges.json#");
    }
}
