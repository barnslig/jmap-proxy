<?php

namespace Barnslig\Jmap\Tests\Core\Stubs;

use Barnslig\Jmap\Core\Schemas\ValidationException;
use Barnslig\Jmap\Core\Schemas\ValidatorInterface;

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
