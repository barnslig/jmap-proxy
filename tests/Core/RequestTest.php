<?php

namespace JP\Tests\JMAP;

use barnslig\JMAP\Core\Invocation;
use barnslig\JMAP\Core\Request;
use barnslig\JMAP\Core\Schemas\ValidationException;
use PHPUnit\Framework\TestCase;

final class RequestTest extends TestCase
{
    public function testRequestCanonicalizesCapabilities()
    {
        $using = ["c", "a"];
        $methodCalls = [];

        $req = new Request($using, $methodCalls);

        $this->assertEquals($req->getUsedCapabilities()->toArray(), ["a", "c"]);
    }

    public function testRequestCreatesInvocations()
    {
        $using = ["urn:ietf:params:jmap:core"];
        $methodCalls = [
            ["Foo/bar", (object)[], "#0"],
            ["Foo/bar", (object)[], "#1"]
        ];

        $req = new Request($using, $methodCalls);

        $this->assertContainsOnlyInstancesOf(Invocation::class, $req->getMethodCalls());
    }
}
