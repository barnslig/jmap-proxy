<?php

namespace JP\Tests\JMAP;

use Ds\Map;
use barnslig\JMAP\Core\Capability;
use PHPUnit\Framework\TestCase;

final class CapabilityTest extends TestCase
{
    public function testSerializesGetCapabilitiesToJson()
    {
        $capability = new class extends Capability {
            public function getCapabilities(): object
            {
                return (object)[
                    "foo" => "bar"
                ];
            }

            public function getMethods(): Map
            {
                return new Map();
            }

            public function getName(): string
            {
                return "urn:ietf:params:jmap:test";
            }
        };

        $this->assertEquals(json_encode($capability), json_encode(["foo" => "bar"]));
    }
}
