<?php

namespace JP\Tests\JMAP;

use Ds\Vector;
use JP\JMAP\Invocation;
use PHPUnit\Framework\TestCase;

final class InvocationTest extends TestCase
{
    public function testResolveResultReferenceInvalidArgumentRaises()
    {
        $args = (object)[
            "foo" => "bar",
            "#foo" => (object)[
                "resultOf" => "#0",
                "name" => "Foo/bar",
                "path" => "/bar/baz"
            ]
        ];
        $i = new Invocation("Foo/bar", $args, "#1");

        $this->expectException(\RuntimeException::class);
        $i->resolveResultReferences(new Vector());
    }

    public function testResolveResultReference()
    {
        $args = (object)[
            "#foo" => (object)[
                "resultOf" => "#0",
                "name" => "Foo/bar",
                "path" => "/bar/baz"
            ]
        ];

        $responses = new Vector([
            new Invocation("Foo/bar", (object)[
                "bar" => (object)[
                    "baz" => "bla"
                ]
            ], "#0")
        ]);

        $i = new Invocation("Foo/bar", $args, "#1");
        $i->resolveResultReferences($responses);

        $this->assertFalse($i->getArguments()->hasKey("#foo"));
        $this->assertEquals($i->getArguments()->get("foo"), "bla");
    }
}
