<?php

namespace barnslig\JMAP\Core\Methods;

use barnslig\JMAP\Core\Invocation;
use barnslig\JMAP\Core\Method;
use barnslig\JMAP\Core\RequestContext;

abstract class QueryChangesMethod implements Method
{
    public function handle(Invocation $request, RequestContext $context): Invocation
    {
        $context->getValidator()->validate($request, "http://jmap.io/methods/queryChanges.json#");

        return $request;
    }
}
