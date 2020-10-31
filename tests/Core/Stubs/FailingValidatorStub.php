<?php

namespace barnslig\JMAP\Tests\Core\Stubs;

use barnslig\JMAP\Core\Schemas\ValidationException;
use barnslig\JMAP\Core\Schemas\ValidatorInterface;

/**
 * A schema validator that is always failing
 */
class FailingValidatorStub implements ValidatorInterface
{
    public function validate($data, string $uri): void
    {
        throw new ValidationException();
    }
}
