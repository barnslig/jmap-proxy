<?php

namespace JP\Tests\JMAP;

use Ds\Vector;
use JP\JMAP\Exceptions\MethodInvocationException;
use JP\JMAP\Invocation;
use JP\JMAP\ResultReference;
use PHPUnit\Framework\TestCase;

final class ResultReferenceTest extends TestCase
{
    /**
     * Create a reference
     *
     * @return object Reference
     */
    protected function createReference(): object
    {
        return (object)[
            "resultOf" => "#0",
            "name" => "Foo/bar",
            "path" => "/bar/baz"
        ];
    }

    public function testValidates()
    {
        $this->expectException(MethodInvocationException::class);
        $reference = (object)[];
        new ResultReference($reference);
    }

    public function testRaiseUnknownInvocation()
    {
        $responses = new Vector();
        $reference = $this->createReference();

        $rr = new ResultReference($reference);
        $this->expectException(MethodInvocationException::class);
        $rr->resolve($responses);
    }

    public function testRaisesUnknownPath()
    {
        $responses = new Vector([
            new Invocation("Foo/bar", [
                "bar" => (object)[
                    "bla" => "bla"
                ]
            ], "#0")
        ]);
        $reference = $this->createReference();

        $rr = new ResultReference($reference);
        $this->expectException(MethodInvocationException::class);
        $rr->resolve($responses);
    }

    public function testResolves()
    {
        $responses = new Vector([
            new Invocation("Foo/bar", [
                "bar" => (object)[
                    "baz" => "bla"
                ]
            ], "#0")
        ]);
        $reference = $this->createReference();

        $rr = new ResultReference($reference);
        $this->assertEquals($rr->resolve($responses), "bla");
    }
}
