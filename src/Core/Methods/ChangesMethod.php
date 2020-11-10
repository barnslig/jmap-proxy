<?php

namespace Barnslig\Jmap\Core\Methods;

use Barnslig\Jmap\Core\Invocation;
use Barnslig\Jmap\Core\Method;
use Barnslig\Jmap\Core\RequestContext;

abstract class ChangesMethod implements Method
{
    public function handle(Invocation $request, RequestContext $context): Invocation
    {
        $context->getValidator()->validate($request, "http://jmap.io/methods/changes.json#");

        return $request;
    }
}
