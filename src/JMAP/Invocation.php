<?php

namespace JP\JMAP;

use Ds\Map;
use Ds\Vector;
use JsonSerializable;

class Invocation implements JsonSerializable
{
    /** @var string */
    private $name = '';

    /** @var object */
    private $arguments = null;

    /** @var string */
    private $methodCallId = '';

    public function __construct(string $name, object $arguments, string $methodCallId)
    {
        $this->name = $name;
        $this->arguments = $arguments;
        $this->methodCallId = $methodCallId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getArguments(): object
    {
        return $this->arguments;
    }

    public function getMethodCallId(): string
    {
        return $this->methodCallId;
    }

    /**
     * Return an instance with the specified arguments
     *
     * @param array $arguments
     * @return static
     */
    public function withArguments(object $arguments)
    {
        $new = clone $this;
        $new->arguments = $arguments;
        return $new;
    }

    /**
     * Return an instance with the specified name
     *
     * @param string $name
     * @return static
     */
    public function withName(string $name)
    {
        $new = clone $this;
        $new->name = $name;
        return $new;
    }

    public function jsonSerialize()
    {
        return [$this->getName(), $this->getArguments(), $this->getMethodCallId()];
    }
}
