<?php

namespace JP\Tests\JMAP;

use Ds\Map;
use Ds\Vector;
use barnslig\JMAP\Core\Capabilities\CoreCapability;
use barnslig\JMAP\Core\Capabilities\CoreCapability\CoreType\CoreEchoMethod;
use barnslig\JMAP\Core\Capability;
use barnslig\JMAP\Core\Exceptions\MethodInvocationException;
use barnslig\JMAP\Core\Exceptions\UnknownCapabilityException;
use barnslig\JMAP\Core\Invocation;
use barnslig\JMAP\Core\Method;
use barnslig\JMAP\Core\Request;
use barnslig\JMAP\Core\Session;
use PHPUnit\Framework\TestCase;

final class SessionTest extends TestCase
{
    /** @var Session */
    protected $session;

    protected function setUp(): void
    {
        $this->session = new Session();
    }

    public function testGetState()
    {
        $state = $this->session->getState();
        $this->assertTrue(mb_strlen($state) == 12);
    }

    public function testResolveMethodsUnknownCapabilityThrows()
    {
        $this->expectException(UnknownCapabilityException::class);

        $this->session->resolveMethods(new Vector(["urn:ietf:params:jmap:test"]));
    }

    public function testResolveMethods()
    {
        $capability = new class extends Capability {
            public function getCapabilities(): object
            {
                return (object)[];
            }

            public function getMethods(): Map
            {
                return new Map([
                    "Foo/bar" => null
                ]);
            }

            public function getName(): string
            {
                return "urn:ietf:params:jmap:test";
            }
        };
        $this->session->addCapability($capability);

        $methods = $this->session->resolveMethods(new Vector(["urn:ietf:params:jmap:test"]));
        $this->assertEquals($methods->toArray(), ["Foo/bar" => null]);
    }
}
