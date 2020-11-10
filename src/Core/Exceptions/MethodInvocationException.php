<?php

namespace Barnslig\Jmap\Core\Exceptions;

use RuntimeException;

/**
 * Exception that is raised within a Method when an Invocation fails
 *
 * @see https://tools.ietf.org/html/rfc8620#section-3.6.2
 */
class MethodInvocationException extends RuntimeException
{
    /** @var string */
    private $type;

    /**
     * Construct a new MethodInvocationException
     *
     * @param string $type Error type, see https://tools.ietf.org/html/rfc8620#section-3.6.2
     * @param mixed ...$params
     */
    public function __construct(string $type, ...$params)
    {
        parent::__construct(...$params);
        $this->type = $type;
    }

    /**
     * Get the error type
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}
