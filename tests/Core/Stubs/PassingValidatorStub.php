<?php

namespace Barnslig\Jmap\Tests\Core\Stubs;

use Barnslig\Jmap\Core\Schemas\ValidatorInterface;

/**
 * A schema validator that is always passing
 */
class PassingValidatorStub implements ValidatorInterface
{
    public function validate($data, string $uri): void
    {
    }
}
