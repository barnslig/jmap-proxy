<?php

namespace JP\Tests\JMAP\Schemas;

use barnslig\JMAP\Core\Schemas\ValidationException;
use barnslig\JMAP\Core\Schemas\Validator;
use PHPUnit\Framework\TestCase;

final class ValidatorTest extends TestCase
{
    public function testValidateRaisesMessage()
    {
        $validator = new Validator();
        $data = (object) [
            "using" => [
                "urn:ietf:params:jmap:core"
            ]
        ];
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage("Error validating #/: The attribute property 'methodCalls' is required.");
        $validator->validate($data, "http://jmap.io/Request.json#");
    }
}