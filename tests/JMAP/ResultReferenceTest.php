<?php

namespace JP\Tests\JMAP;

use Ds\Vector;
use JP\JMAP\Invocation;
use JP\JMAP\ResultReference;
use JsonSchema\Exception\ValidationException;
use PHPUnit\Framework\TestCase;

final class ResultReferenceTest extends TestCase
{
    public function testValidates()
    {
        $this->expectException(ValidationException::class);
        $reference = (object)[];
        new ResultReference($reference);
    }

    public function testRaiseUnknownInvocation()
    {
        $responses = new Vector();
        $reference = (object)[
            "resultOf" => "#0",
            "name" => "Foo/bar",
            "path" => "/bar/baz"
        ];
        $rr = new ResultReference($reference);

        $this->expectException(\RuntimeException::class);
        $rr->resolve($responses);
    }

    public function testResolves()
    {
        $responses = new Vector([
            new Invocation("Foo/bar", (object)[
                "bar" => (object)[
                    "baz" => "bla"
                ]
            ], "#0")
        ]);
        $reference = (object)[
            "resultOf" => "#0",
            "name" => "Foo/bar",
            "path" => "/bar/baz"
        ];
        $rr = new ResultReference($reference);
        $this->assertEquals($rr->resolve($responses), "bla");
    }
}
