<?php

namespace barnslig\JMAP\Core;

use barnslig\JMAP\Core\Schemas\ValidatorInterface;
use barnslig\JMAP\Core\Session;

/**
 * JMAP request context
 */
class RequestContext
{
    /**
     * JMAP session used to process the request
     *
     * @var Session
     */
    private $session;

    /**
     * JSON Schema validator used to check input data
     *
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * Construct a new RequestContext
     *
     * @param Session $session JMAP session
     * @param ValidatorInterface $validator JSON Schema validator
     */
    public function __construct(Session $session, ValidatorInterface $validator)
    {
        $this->session = $session;
        $this->validator = $validator;
    }

    /**
     * Get the JMAP session
     *
     * @return Session
     */
    public function getSession(): Session
    {
        return $this->session;
    }

    /**
     * Get the JSON Schema validator
     *
     * @return ValidatorInterface
     */
    public function getValidator(): ValidatorInterface
    {
        return $this->validator;
    }
}
