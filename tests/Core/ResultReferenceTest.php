<?php

namespace Barnslig\Jmap\Tests;

use Ds\Vector;
use Barnslig\Jmap\Core\Exceptions\MethodInvocationException;
use Barnslig\Jmap\Core\Invocation;
use Barnslig\Jmap\Core\ResultReference;
use PHPUnit\Framework\TestCase;

final class ResultReferenceTest extends TestCase
{
    /** @var ResultReference */
    protected $rr;

    protected function setUp(): void
    {
        $this->rr = new ResultReference("#0", "Foo/bar", "/bar/baz");
    }

    public function testRaiseUnknownInvocation()
    {
        $responses = new Vector();

        $this->expectException(MethodInvocationException::class);
        $this->rr->resolve($responses);
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

        $this->expectException(MethodInvocationException::class);
        $this->rr->resolve($responses);
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

        $this->assertEquals($this->rr->resolve($responses), "bla");
    }
}
