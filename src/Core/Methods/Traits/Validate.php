<?php

namespace barnslig\JMAP\Core\Methods\Traits;

use barnslig\JMAP\Core\Exceptions\MethodInvocationException;
use barnslig\JMAP\Core\Invocation;
use barnslig\JMAP\Core\Schemas\ValidationException;
use barnslig\JMAP\Core\Schemas\Validator;

/**
 * Trait with functions related to schema validation
 */
trait Validate
{
    /**
     * Validate the arguments of an Invocation according to a JSON schema
     *
     * @param Invocation $request Request invocation
     * @param string $schema - URI of the JSON schema, e.g. http://jmap.io/methods/get.json#
     * @throws MethodInvocationException When the arguments of the request invocation are not valid
     * @return Invocation The request invocation, aka $request
     */
    public function validate(Invocation $request, string $schema): Invocation
    {
        $validator = new Validator();
        try {
            $validator->validate($request->getArguments(), $schema);
        } catch (ValidationException $exception) {
            throw new MethodInvocationException("invalidArguments", $exception->getMessage());
        }

        return $request;
    }
}
