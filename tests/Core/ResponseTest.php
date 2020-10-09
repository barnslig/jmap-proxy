<?php

namespace JP\Tests\JMAP;

use Ds\Vector;
use barnslig\JMAP\Core\Response;
use barnslig\JMAP\Core\Session;
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
