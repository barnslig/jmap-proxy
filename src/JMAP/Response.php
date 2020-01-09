<?php

namespace JP\JMAP;

use Ds\Vector;
use JP\JMAP\Invocation;
use JP\JMAP\Session;
use JsonSerializable;

class Response implements JsonSerializable
{
    /** @var Session */
    private $session;

    /**
     * Method responses Vector consisting of Invocation instances
     *
     * @var Vector */
    private $methodResponses;

    public function __construct(Session $session)
    {
        $this->session = $session;
        $this->methodResponses = new Vector();
    }

    /**
     * Add a method response to the response
     *
     * @param Invocation $methodResponse
     */
    public function addMethodResponse(Invocation $methodResponse)
    {
        $this->methodResponses->push($methodResponse);
    }

    public function getMethodResponses(): Vector
    {
        return $this->methodResponses;
    }

    public function jsonSerialize()
    {
        return [
            "methodResponses" => $this->methodResponses,
            "createdIds" => (object)[],
            "sessionState" => $this->session->getState()
        ];
    }
}
