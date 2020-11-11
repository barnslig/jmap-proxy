<?php

namespace Barnslig\Jmap\Tests\Core;

use Barnslig\Jmap\Core\Capabilities\CoreCapability\CoreType\CoreEchoMethod;
use Barnslig\Jmap\Core\Capability;
use Barnslig\Jmap\Core\Exceptions\UnknownCapabilityException;
use Barnslig\Jmap\Core\Session;
use Ds\Map;
use Ds\Vector;
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
                    "Foo/bar" => CoreEchoMethod::class
                ]);
            }

            public function getName(): string
            {
                return "urn:ietf:params:jmap:test";
            }
        };
        $this->session->addCapability($capability);

        $methods = $this->session->resolveMethods(new Vector(["urn:ietf:params:jmap:test"]));
        $this->assertEquals($methods->toArray(), ["Foo/bar" => CoreEchoMethod::class]);
    }
}
