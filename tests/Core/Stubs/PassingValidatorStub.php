<?php

namespace barnslig\JMAP\Tests\Core\Stubs;

use barnslig\JMAP\Core\Schemas\ValidatorInterface;

/**
 * A schema validator that is always passing
 */
class PassingValidatorStub implements ValidatorInterface
{
    public function validate($data, string $uri): void
    {
    }
}
