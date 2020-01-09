<?php

namespace JP\JMAP;

use Ds\Map;
use Ds\Vector;
use JsonSerializable;

class Invocation implements JsonSerializable
{
    /** @var string */
    private $name = '';

    /** @var Map */
    private $arguments = null;

    /** @var string */
    private $methodCallId = '';

    public function __construct(string $name, object $arguments, string $methodCallId)
    {
        $this->name = $name;
        $this->arguments = new Map(json_decode(json_encode($arguments), true));
        $this->methodCallId = $methodCallId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getArguments(): Map
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

    /**
     * Resolve result references based on a Vector of Invocations
     *
     * See 3.7 References to Previous Method Results of the Core spec
     * @param Vector $responses Vector of already computed Invocation response instances
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
                throw new \RuntimeException("invalidArguments: The key '" . $key . "' is contained both in normal and referenced form.");
            }

            $ref = new ResultReference((object)$value);
            $this->arguments->put($key, $ref->resolve($responses));
        }
    }

    public function jsonSerialize()
    {
        return [$this->getName(), $this->getArguments(), $this->getMethodCallId()];
    }
}
