<?php

namespace JP\Tests\JMAP\Controllers;

use barnslig\JMAP\Core\Controllers\SessionController;
use barnslig\JMAP\Core\Request;
use barnslig\JMAP\Core\RequestContext;
use barnslig\JMAP\Core\Schemas\ValidatorInterface;
use barnslig\JMAP\Core\Session;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

final class SessionControllerTest extends TestCase
{
    /**
     * Create a validator that is always passing
     *
     * @return ValidatorInterface Always passing validator
     */
    protected function createPassingValidator(): ValidatorInterface
    {
        return new class implements ValidatorInterface
        {
            public function validate($data, string $uri): void
            {
            }
        };
    }

    public function setUp(): void
    {
        $this->session = new Session();
        $this->validator = $this->createPassingValidator();
        $this->context = new RequestContext($this->session, $this->validator);

        $this->controller = new SessionController($this->context, []);
    }

    public function testHandle()
    {
        $req = $this->prophesize(ServerRequestInterface::class);

        $res = $this->controller->handle($req->reveal());

        $this->assertEquals($res->getStatusCode(), 200);
        $this->assertEquals($res->getHeader("Content-Type"), ["application/json"]);
        $this->assertEquals($res->getHeader("Cache-Control"), ["no-cache, no-store, must-revalidate"]);

        $this->assertEquals($res->getBody(), json_encode($this->session));
    }
}
