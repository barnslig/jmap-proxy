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

    /**
     * Asserts that an Invocation is equal to an Array representation
     *
     * @param Invocation $actual
     * @param array<mixed> $expected
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    protected function assertInvocation(Invocation $actual, array $expected)
    {
        $arrActual = json_decode(json_encode($actual, JSON_THROW_ON_ERROR));
        $this->assertEquals($arrActual, $expected);
    }

    /**
     * Create a JMAP method that raises an exception
     *
     * @return Method Raising method
     */
    protected function createRaisingMethod(): Method
    {
        return new class implements Method {
            public function handle(Invocation $request, Session $session): Invocation
            {
                throw new MethodInvocationException("test", "testmessage");
            }
        };
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

    public function testResolveMethodCallUnknownMethod()
    {
        $methodCall = new Invocation("Foo/bar", [], "#0");
        $methodResponses = new Vector();
        $methods = new Map();

        $response = $this->session->resolveMethodCall($methodCall, $methodResponses, $methods);

        $this->assertInvocation($response, ["error", (object)["type" => "unknownMethod"], "#0"]);
    }

    public function testResolveMethodCallInvocationException()
    {
        $methodCall = new Invocation("Foo/raising", [], "#0");
        $methodResponses = new Vector();
        $methods = new Map([
            "Foo/raising" => $this->createRaisingMethod()
        ]);

        $response = $this->session->resolveMethodCall($methodCall, $methodResponses, $methods);

        $this->assertInvocation($response, ["error", (object)["type" => "test", "description" => "testmessage"], "#0"]);
    }

    public function testResolveMethodCall()
    {
        $methodCall = new Invocation("Core/echo", [
            "#bla" => (object)[
                "resultOf" => "#0",
                "name" => "Core/echo",
                "path" => "/bar"
            ]
        ], "#1");
        $methodResponses = new Vector([
            new Invocation("Core/echo", ["bar" => "baz"], "#0")
        ]);
        $methods = new Map([
            "Core/echo" => CoreEchoMethod::class
        ]);

        $response = $this->session->resolveMethodCall($methodCall, $methodResponses, $methods);

        $this->assertInvocation($response, ["Core/echo", (object)["bla" => "baz"], "#1"]);
    }

    public function testDispatch()
    {
        // 1. add core capability with Core/echo method
        $this->session->addCapability(new CoreCapability());

        // 2. create request that calls Core/echo method
        $request = new Request((object)[
            "using" => ["urn:ietf:params:jmap:core"],
            "methodCalls" => [
                [
                    "Core/echo",
                    (object)["bar" => "baz"],
                    "#0"
                ],
                [
                    "Core/echo",
                    (object)[
                        "#bla" => (object)[
                            "resultOf" => "#0",
                            "name" => "Core/echo",
                            "path" => "/bar"
                        ]
                    ],
                    "#1"
                ]
            ]
        ], 2);

        // 3. dispatch request
        $response = $this->session->dispatch($request);

        // 4. compare result
        $expectedResponse = [
            "methodResponses" => [
                ["Core/echo", ["bar" => "baz"], "#0"],
                ["Core/echo", ["bla" => "baz"], "#1"]
            ],
            "createdIds" => (object)[],
            "sessionState" => $this->session->getState()
        ];

        $this->assertEquals(
            json_encode($response),
            json_encode($expectedResponse)
        );
    }
}
