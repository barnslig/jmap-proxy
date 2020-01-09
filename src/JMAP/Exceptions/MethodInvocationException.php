<?php

namespace JP\JMAP\Exceptions;

use RuntimeException;

/**
 * Exception that is raised when an Invocation fails
 */
class MethodInvocationException extends RuntimeException
{
    /** @var string */
    private $type;

    public function __construct(string $type, ...$params)
    {
        parent::__construct(...$params);
        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
