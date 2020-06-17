<?php

namespace barnslig\JMAP\Core\Methods;

use barnslig\JMAP\Core\Exceptions\MethodInvocationException;
use barnslig\JMAP\Core\Invocation;
use barnslig\JMAP\Core\Method;
use barnslig\JMAP\Core\Schemas\ValidationException;
use barnslig\JMAP\Core\Schemas\Validator;
use barnslig\JMAP\Core\Session;

abstract class GetMethod implements Method
{
    public function getName(): string
    {
        return "get";
    }

    public function handle(Invocation $request, Session $session): Invocation
    {
        $validator = new Validator();
        try {
            $validator->validate($request->getArguments(), "http://jmap.io/methods/get.json#");
        } catch (ValidationException $exception) {
            throw new MethodInvocationException("invalidArguments", $exception->getMessage());
        }

        return $request;
    }
}
