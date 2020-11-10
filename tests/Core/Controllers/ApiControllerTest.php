<?php

namespace Barnslig\Jmap\Tests\Core\Controllers;

use Barnslig\Jmap\Core\Capabilities\CoreCapability;
use Barnslig\Jmap\Core\Capabilities\CoreCapability\CoreType\CoreEchoMethod;
use Barnslig\Jmap\Core\Controllers\ApiController;
use Barnslig\Jmap\Core\Invocation;
use Barnslig\Jmap\Core\Request;
use Barnslig\Jmap\Core\RequestContext;
use Barnslig\Jmap\Core\Schemas\ValidatorInterface;
use Barnslig\Jmap\Core\Session;
use Barnslig\Jmap\Tests\Core\Stubs\FailingValidatorStub;
use Barnslig\Jmap\Tests\Core\Stubs\PassingValidatorStub;
use Barnslig\Jmap\Tests\Core\Stubs\RaisingMethodStub;
use Ds\Map;
use Ds\Vector;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

final class ApiControllerTest extends TestCase
{
    /** @var Session */
    private $session;

    /** @var ValidatorInterface */
    private $validator;

    /** @var RequestContext */
    private $context;

    /** @var ApiController */
    private $controller;

    public function setUp(): void
    {
        $this->session = new Session();
        $this->validator = new PassingValidatorStub();
        $this->context = new RequestContext($this->session, $this->validator);

        $this->controller = new ApiController($this->context, [
            'maxCallsInRequest' => 16,
        ]);
    }

    /**
     * Assert that two objects are equal via their JSON representation
     *
     * @param mixed $actual
     * @param mixed $expected
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    protected function assertEqualsViaJson($actual, $expected)
    {
        $this->assertEquals(
            json_decode(json_encode($actual) ?: "null", false, 512, JSON_THROW_ON_ERROR),
            json_decode(json_encode($expected) ?: "null", false, 512, JSON_THROW_ON_ERROR)
        );
    }

    public function testParseJsonBodyChecksContentType()
    {
        $req = $this->prophesize(ServerRequestInterface::class);
        $req->getHeaderLine("Content-Type")->willReturn("text/html");

        $this->expectException(\RuntimeException::class);

        $this->controller->parseJsonBody($req->reveal());
    }

    public function testParseJsonBody()
    {
        $expected = [
            "foo" => "bar"
        ];

        $req = $this->prophesize(ServerRequestInterface::class);
        $req->getHeaderLine("Content-Type")->willReturn("application/json");
        $req->getBody()->willReturn(json_encode($expected));

        $json = $this->controller->parseJsonBody($req->reveal());

        $this->assertEqualsViaJson($json, $expected);
    }

    public function testResolveMethodCallNotImplementedRaises()
    {
        $methodCall = new Invocation("Foo/bar", [], "#0");
        $methodResponses = new Vector();
        $methods = new Map([
            "Foo/bar" => "notexisting"
        ]);

        $this->expectException(\BadMethodCallException::class);

        // @phpstan-ignore-next-line
        $response = $this->controller->resolveMethodCall($methodCall, $methodResponses, $methods);
    }

    public function testResolveMethodCallUnknownMethodError()
    {
        $methodCall = new Invocation("Foo/bar", [], "#0");
        $methodResponses = new Vector();
        $methods = new Map();

        $response = $this->controller->resolveMethodCall($methodCall, $methodResponses, $methods);

        $this->assertEqualsViaJson($response, ["error", ["type" => "unknownMethod"], "#0"]);
    }

    public function testResolveMethodCallInvocationError()
    {
        $methodCall = new Invocation("Foo/raising", [], "#0");
        $methodResponses = new Vector();
        $methods = new Map([
            "Foo/raising" => RaisingMethodStub::class
        ]);

        $response = $this->controller->resolveMethodCall($methodCall, $methodResponses, $methods);

        $this->assertEqualsViaJson($response, ["error", ["type" => "test", "description" => "testmessage"], "#0"]);
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

        $response = $this->controller->resolveMethodCall($methodCall, $methodResponses, $methods);

        $this->assertEqualsViaJson($response, ["Core/echo", ["bla" => "baz"], "#1"]);
    }

    public function testDispatch()
    {
        // 1. add core capability with Core/echo method
        $this->session->addCapability(new CoreCapability());

        // 2. create request that calls Core/echo method
        $using = ["urn:ietf:params:jmap:core"];
        $methodCalls = [
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
        ];
        $request = new Request($using, $methodCalls);

        // 3. dispatch request
        $response = $this->controller->dispatch($request);

        // 4. compare result
        $expectedResponse = [
            "methodResponses" => [
                ["Core/echo", ["bar" => "baz"], "#0"],
                ["Core/echo", ["bla" => "baz"], "#1"]
            ],
            "createdIds" => (object)[],
            "sessionState" => $this->session->getState()
        ];

        $this->assertEqualsViaJson($response, $expectedResponse);
    }

    public function testHandleNotJson()
    {
        $req = $this->prophesize(ServerRequestInterface::class);
        $req->getHeaderLine("Content-Type")->willReturn("application/json");
        $req->getBody()->willReturn(",");

        $res = $this->controller->handle($req->reveal());

        $this->assertEquals($res->getStatusCode(), 400);
        $this->assertEquals($res->getHeader("Content-Type"), ["application/problem+json"]);

        $this->assertEquals(json_decode($res->getBody()), (object)[
            "type" => "urn:ietf:params:jmap:error:notRequest",
            "status" => 400,
            "detail" =>
                "The content type of the request was not application/json or the request did not parse as I-JSON."
        ]);
    }

    public function testHandleNotRequest()
    {
        $this->validator = new FailingValidatorStub();
        $this->context = new RequestContext($this->session, $this->validator);
        $this->controller = new ApiController($this->context, []);

        $req = $this->prophesize(ServerRequestInterface::class);
        $req->getHeaderLine("Content-Type")->willReturn("application/json");
        $req->getBody()->willReturn("{}");

        $res = $this->controller->handle($req->reveal());

        $this->assertEquals($res->getStatusCode(), 400);
        $this->assertEquals($res->getHeader("Content-Type"), ["application/problem+json"]);

        $this->assertEquals(json_decode($res->getBody()), (object)[
            "type" => "urn:ietf:params:jmap:error:notRequest",
            "status" => 400,
            "detail" => ""
        ]);
    }

    public function testHandleTooManyCalls()
    {
        $this->controller = new ApiController($this->context, [
            'maxCallsInRequest' => 0
        ]);

        $req = $this->prophesize(ServerRequestInterface::class);
        $req->getHeaderLine("Content-Type")->willReturn("application/json");
        $req->getBody()->willReturn('{
            "using": [
                "urn:ietf:params:jmap:core"
            ],
            "methodCalls": [
                ["Core/echo", { "foo": "bar" }, "#1"],
                ["Core/echo", { "foo": "bar" }, "#2"]
            ]
        }');

        $res = $this->controller->handle($req->reveal());

        $this->assertEquals($res->getStatusCode(), 400);
        $this->assertEquals($res->getHeader("Content-Type"), ["application/problem+json"]);

        $this->assertEquals(json_decode($res->getBody()), (object)[
            "type" => "urn:ietf:params:jmap:error:limit",
            "status" => 400,
            "detail" => "The maximum number of 0 method calls got exceeded.",
            "limit" => "maxCallsInRequest"
        ]);
    }

    public function testHandleUnknownCapability()
    {
        $req = $this->prophesize(ServerRequestInterface::class);
        $req->getHeaderLine("Content-Type")->willReturn("application/json");
        $req->getBody()->willReturn('{
            "using": [
                "urn:ietf:params:jmap:test-unknown"
            ],
            "methodCalls": []
        }');

        $res = $this->controller->handle($req->reveal());

        $this->assertEquals($res->getStatusCode(), 400);
        $this->assertEquals($res->getHeader("Content-Type"), ["application/problem+json"]);

        $this->assertEquals(json_decode($res->getBody()), (object)[
            "type" => "urn:ietf:params:jmap:error:unknownCapability",
            "status" => 400,
            "detail" =>
                "The Request object used capability 'urn:ietf:params:jmap:test-unknown', " .
                "which is not supported by this server"
        ]);
    }

    public function testHandle()
    {
        $this->session->addCapability(new CoreCapability());

        $req = $this->prophesize(ServerRequestInterface::class);
        $req->getHeaderLine("Content-Type")->willReturn("application/json");
        $req->getBody()->willReturn('{
            "using": [
                "urn:ietf:params:jmap:core"
            ],
            "methodCalls": [
                [
                    "Core/echo",
                    {
                        "foo": {
                            "bar": {
                                "baz": "lol"
                            }
                        }
                    },
                    "#1"
                ],
                [
                    "Core/echo",
                    {
                        "#test": {
                            "resultOf": "#1",
                            "name": "Core/echo",
                            "path": "/foo/bar/baz"
                        }
                    },
                    "#2"
                ]
            ]
        }');

        $res = $this->controller->handle($req->reveal());

        $this->assertEquals($res->getStatusCode(), 200);
        $this->assertEquals($res->getHeader("Content-Type"), ["application/json"]);

        $decodedRes = json_decode($res->getBody());
        $this->assertEquals($decodedRes->methodResponses, [
            [
                "Core/echo", (object)[
                    "foo" => (object)[
                        "bar" => (object)[
                            "baz" => "lol"
                        ]
                    ]
                ],
                "#1"
            ],
            [
                "Core/echo", (object)[
                    "test" => "lol"
                ],
                "#2"
            ]
        ]);
    }
}
