<?php

namespace barnslig\JMAP\Tests\Core\Controllers;

use barnslig\JMAP\Core\Controllers\SessionController;
use barnslig\JMAP\Core\RequestContext;
use barnslig\JMAP\Core\Schemas\ValidatorInterface;
use barnslig\JMAP\Core\Session;
use barnslig\JMAP\Tests\Core\Stubs\PassingValidatorStub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

final class SessionControllerTest extends TestCase
{
    /** @var Session */
    protected $session;

    /** @var ValidatorInterface */
    protected $validator;

    /** @var RequestContext */
    protected $context;

    /** @var SessionController */
    protected $controller;

    public function setUp(): void
    {
        $this->session = new Session();
        $this->validator = new PassingValidatorStub();
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
