<?php

namespace Barnslig\Jmap\Tests;

use Ds\Vector;
use Barnslig\Jmap\Core\Response;
use Barnslig\Jmap\Core\Session;
use PHPUnit\Framework\TestCase;

final class ResponseTest extends TestCase
{
    public function testSerializesToJson()
    {
        $session = new Session();
        $methodResponses = new Vector();

        $res = new Response($session, $methodResponses);

        $this->assertEquals(
            json_encode($res),
            json_encode([
                "methodResponses" => $methodResponses,
                "createdIds" => (object)[],
                "sessionState" => $session->getState()
            ])
        );
    }
}
