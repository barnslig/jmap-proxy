<?php

namespace Barnslig\Jmap\Core\Methods;

use Barnslig\Jmap\Core\Invocation;
use Barnslig\Jmap\Core\Method;
use Barnslig\Jmap\Core\RequestContext;

abstract class QueryChangesMethod implements Method
{
    public static function validate(Invocation $request, RequestContext $context): void
    {
        $context->getValidator()->validate($request->getArguments(), "http://jmap.io/methods/queryChanges.json#");
    }

    abstract public function handle(Invocation $request, RequestContext $context): Invocation;
}
