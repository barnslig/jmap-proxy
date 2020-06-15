<?php

namespace JP\JMAP;

use Ds\Vector;
use JsonSerializable;

/**
 * JMAP Response
 *
 * @see https://tools.ietf.org/html/rfc8620#section-3.4
 */
class Response implements JsonSerializable
{
    /** @var Session */
    private $session;

    /**
     * Method responses Vector
     *
     * @var Vector<Invocation> */
    private $methodResponses;

    /**
     * Construct a new Response instance
     *
     * @param Session $session Current session, used to determine the `sessionState`
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
        $this->methodResponses = new Vector();
    }

    /**
     * Add a method response to the response
     *
     * @param Invocation $methodResponse
     * @return void
     */
    public function addMethodResponse(Invocation $methodResponse)
    {
        $this->methodResponses->push($methodResponse);
    }

    /**
     * Get all method responses
     *
     * @return Vector<Invocation> Vector of method responses
     */
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
