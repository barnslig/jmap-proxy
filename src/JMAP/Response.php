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
     * Method responses Vector consisting of Invocation instances
     *
     * @var Vector */
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
     */
    public function addMethodResponse(Invocation $methodResponse)
    {
        $this->methodResponses->push($methodResponse);
    }

    /**
     * Get all method responses
     *
     * @return Vector A Vector of Invocations
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
