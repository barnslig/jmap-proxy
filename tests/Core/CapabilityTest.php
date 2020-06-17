<?php

namespace JP\Tests\JMAP;

use Ds\Map;
use barnslig\JMAP\Core\Capability;
use barnslig\JMAP\Core\Invocation;
use barnslig\JMAP\Core\Method;
use barnslig\JMAP\Core\Session;
use barnslig\JMAP\Core\Type;
use PHPUnit\Framework\TestCase;

final class CapabilityTest extends TestCase
{
    public function testGetMethods()
    {
        $method = new class implements Method {
            public function getName(): string
            {
                return "echo";
            }

            public function handle(Invocation $request, Session $session): Invocation
            {
                return $request;
            }
        };

        $type = new class implements Type {
            public function getName(): string
            {
                return "Test";
            }
        };

        $capability = new class extends Capability
        {
            public function getCapabilities(): object
            {
                return (object) [];
            }

            public function getName(): string
            {
                return "urn:ietf:params:jmap:test";
            }
        };
        $capability->addType($type, [$method]);

        $this->assertEquals($capability->getMethods()->toArray(), [
            "Test/echo" => $method
        ]);
    }
}
