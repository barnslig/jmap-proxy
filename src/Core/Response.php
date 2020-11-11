<?php

namespace Barnslig\Jmap\Core;

use Ds\Vector;
use JsonSerializable;
use Laminas\Diactoros\Response\JsonResponse;

/**
 * JMAP Response
 *
 * @see https://tools.ietf.org/html/rfc8620#section-3.4
 */
class Response extends JsonResponse implements JsonSerializable
{
    /** @var Session */
    private $session;

    /**
     * Method responses Vector
     *
     * @var Vector<Invocation>
     */
    private $methodResponses;

    /**
     * Construct a new Response instance
     *
     * @param Session $session Current session, used to determine the `sessionState`
     * @param Vector<Invocation> $methodResponses Method responses
     */
    public function __construct(Session $session, Vector $methodResponses)
    {
        $this->session = $session;
        $this->methodResponses = $methodResponses;

        parent::__construct($this);
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
