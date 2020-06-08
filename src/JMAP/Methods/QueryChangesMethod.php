<?php

namespace JP\JMAP\Methods;

use JP\JMAP\Exceptions\MethodInvocationException;
use JP\JMAP\Invocation;
use JP\JMAP\Method;
use JP\JMAP\Schemas\ValidationException;
use JP\JMAP\Schemas\Validator;
use JP\JMAP\Session;

abstract class QueryChangesMethod implements Method
{
    public function getName(): string
    {
        return "queryChanges";
    }

    public function handle(Invocation $request, Session $session): Invocation
    {
        $validator = new Validator();
        try {
            $validator->validate($request->getArguments(), "http://jmap.io/methods/queryChanges.json#");
        } catch (ValidationException $exception) {
            throw new MethodInvocationException("invalidArguments", $exception->getMessage());
        }

        return $request;
    }
}
