<?php

namespace JP\Tests\JMAP;

use Ds\Map;
use Ds\Vector;
use JP\JMAP\Capability;
use JP\JMAP\Invocation;
use JP\JMAP\Method;
use JP\JMAP\Exceptions\UnknownCapabilityException;
use JP\JMAP\Exceptions\MethodInvocationException;
use JP\JMAP\Request;
use JP\JMAP\Session;
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
     * @param array $expected
     * @throws PHPUnit\Framework\ExpectationFailedException
     */
    protected function assertInvocation(Invocation $actual, array $expected)
    {
        $arrActual = json_decode(json_encode($actual));
        $this->assertEquals($arrActual, $expected);
    }

    /**
     * Create a JMAP echo method
     *
     * @return Method Echo method
     */
    protected function createEchoMethod(): Method
    {
        return new class implements Method {
            public function getName(): string
            {
                return "echo";
            }

            public function handle(Invocation $request, Session $session): Invocation
            {
                return $request;
            }
        };
    }

    /**
     * Create a JMAP method that raises an exception
     *
     * @return Method Raising method
     */
    protected function createRaisingMethod(): Method
    {
        return new class implements Method {
            public function getName(): string
            {
                return "raising";
            }

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
        $capabilityMethods = ["Foo/bar" => null];

        $capability = $this->createStub(Capability::class);
        $capability->method('getMethods')
            ->willReturn(new Map($capabilityMethods));
        $capability->method("getName")
            ->willReturn("urn:ietf:params:jmap:test");

        $this->session->addCapability($capability);

        $methods = $this->session->resolveMethods(new Vector(["urn:ietf:params:jmap:test"]));
        $this->assertEquals($methods->toArray(), $capabilityMethods);
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
        $methodCall = new Invocation("Foo/echo", [
            "#bla" => (object)[
                "resultOf" => "#0",
                "name" => "Foo/echo",
                "path" => "/bar"
            ]
        ], "#1");
        $methodResponses = new Vector([
            new Invocation("Foo/echo", ["bar" => "baz"], "#0")
        ]);
        $methods = new Map([
            "Foo/echo" => $this->createEchoMethod()
        ]);

        $response = $this->session->resolveMethodCall($methodCall, $methodResponses, $methods);

        $this->assertInvocation($response, ["Foo/echo", (object)["bla" => "baz"], "#1"]);
    }

    public function testDispatch()
    {
        // 1. add test capability with Foo/bar method
        $capability = $this->createStub(Capability::class);
        $capability->method("getMethods")
            ->willReturn(new Map([
                "Foo/echo" => $this->createEchoMethod()
            ]));
        $capability->method("getName")
            ->willReturn("urn:ietf:params:jmap:test");

        $this->session->addCapability($capability);

        // 2. create request that calls Foo/echo method
        $request = new Request((object)[
            "using" => ["urn:ietf:params:jmap:test"],
            "methodCalls" => [
                [
                    "Foo/echo",
                    (object)["bar" => "baz"],
                    "#0"
                ],
                [
                    "Foo/echo",
                    (object)[
                        "#bla" => (object)[
                            "resultOf" => "#0",
                            "name" => "Foo/echo",
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
        $methodResponses = $response->getMethodResponses();
        $this->assertInvocation($methodResponses[0], ["Foo/echo", (object)["bar" => "baz"], "#0"]);
        $this->assertInvocation($methodResponses[1], ["Foo/echo", (object)["bla" => "baz"], "#1"]);
    }
}
