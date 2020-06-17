<?php

namespace JP\Tests\JMAP;

use barnslig\JMAP\Core\Invocation;
use barnslig\JMAP\Core\Request;
use barnslig\JMAP\Core\Schemas\ValidationException;
use PHPUnit\Framework\TestCase;

final class RequestTest extends TestCase
{
    public function testRequestValidates()
    {
        $data = (object)[];
        $this->expectException(ValidationException::class);
        new Request($data, 16);
    }

    public function testRequestCanonicalizesCapabilities()
    {
        $data = (object)[
            "using" => [
                "c",
                "a"
            ],
            "methodCalls" => []
        ];
        $req = new Request($data, 16);

        $this->assertEquals($req->getUsedCapabilities()->toArray(), ["a", "c"]);
    }

    public function testRequestLimitsMethodcalls()
    {
        $data = (object)[
            "using" => [
                "urn:ietf:params:jmap:core"
            ],
            "methodCalls" => [
                ["Foo/bar", (object)[], "#0"],
                ["Foo/bar", (object)[], "#1"]
            ]
        ];

        $this->expectException(\OverflowException::class);
        new Request($data, 1);
    }

    public function testRequestCreatesInvocations()
    {
        $data = (object)[
            "using" => [
                "urn:ietf:params:jmap:core"
            ],
            "methodCalls" => [
                ["Foo/bar", (object)[], "#0"],
                ["Foo/bar", (object)[], "#1"]
            ]
        ];
        $req = new Request($data, 16);
        $this->assertContainsOnlyInstancesOf(Invocation::class, $req->getMethodCalls());
    }
}