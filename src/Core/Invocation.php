<?php

namespace barnslig\JMAP\Core;

use Ds\Map;
use Ds\Vector;
use barnslig\JMAP\Core\Exceptions\MethodInvocationException;
use JsonSerializable;

/**
 * JMAP method calls and responses are represented as Invocation
 *
 * @see https://tools.ietf.org/html/rfc8620#section-3.2
 */
class Invocation implements JsonSerializable
{
    /** @var string */
    private $name = '';

    /** @var Map<string, mixed> */
    private $arguments = null;

    /** @var string */
    private $methodCallId = '';

    /**
     * Construct a new Invocation
     *
     * @param string $name Method name, e.g. "Mailbox/get"
     * @param array<string, mixed> $arguments Method arguments/response
     * @param string $methodCallId Client-provided Method Call ID, e.g. "#0"
     */
    public function __construct(string $name, array $arguments, string $methodCallId)
    {
        $this->name = $name;
        $this->arguments = new Map($arguments);
        $this->methodCallId = $methodCallId;
    }

    /**
     * Get the invocation's name, e.g. "Mailbox/get"
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the invocation's arguments
     *
     * @return Map<string, mixed>
     */
    public function getArguments(): Map
    {
        return $this->arguments;
    }

    /**
     * Get the method call ID which is an arbitrary string from the client
     *
     * @return string
     */
    public function getMethodCallId(): string
    {
        return $this->methodCallId;
    }

    /**
     * Return a new instance with the specified arguments
     *
     * @param array<string, mixed> $arguments
     * @return static
     */
    public function withArguments(array $arguments)
    {
        $new = clone $this;
        $new->arguments = new Map($arguments);
        return $new;
    }

    /**
     * Return a new instance with the specified name
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

    /**
     * Resolve result references based on a Vector of Invocations
     *
     * @see https://tools.ietf.org/html/rfc8620#section-3.7
     * @param Vector<Invocation> $responses Vector of already computed Invocation response instances
     * @throws MethodInvocationException When a key is contained both in normal and referenced form
     * @return void
     */
    public function resolveResultReferences(Vector $responses)
    {
        $mArgs = $this->getArguments();
        // TODO do we need to deep-traverse the arguments?
        foreach ($mArgs as $key => $value) {
            if (mb_substr($key, 0, 1) !== "#") {
                continue;
            }

            $key = mb_substr($key, 1);
            if ($mArgs->hasKey($key)) {
                throw new MethodInvocationException(
                    "invalidArguments",
                    "The key '" . $key . "' is contained both in normal and referenced form."
                );
            }

            $ref = new ResultReference($value);
            $this->arguments->remove("#" . $key);
            $this->arguments->put($key, $ref->resolve($responses));
        }
    }

    public function jsonSerialize()
    {
        return [$this->getName(), $this->getArguments(), $this->getMethodCallId()];
    }
}
