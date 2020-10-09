<?php

namespace barnslig\JMAP\Core;

use barnslig\JMAP\Core\Exceptions\MethodInvocationException;
use Ds\Vector;

/**
 * Result reference
 *
 * This class implements resolving the Result Reference
 *
 * @see https://tools.ietf.org/html/rfc8620#section-3.7
 */
class ResultReference
{
    /** @var string */
    private $resultOf;

    /** @var string */
    private $name;

    /** @var string */
    private $path;

    /**
     * Construct a new ResultReference
     *
     * @param string $resultOf Method call ID of a previous method call in the current request
     * @param string $name Required name of a response to that method call
     * @param string $path A pointer into the arguments of the response selected via the name and resultOf properties
     */
    public function __construct(string $resultOf, string $name, string $path)
    {
        $this->resultOf = $resultOf;
        $this->name = $name;
        $this->path = $path;
    }

    /**
     * Resolve the reference from a Vector of response Invocations
     *
     * @param Vector<Invocation> $responses Vector of already computed response Invocations
     * @throws MethodInvocationException When there is not matching response for the methodCallId/name combination
     * @throws MethodInvocationException When the path could not be resolved
     * @return mixed Result at pointer location
     */
    public function resolve(Vector $responses)
    {
        // 1. Find the first response where methodCallId and name match
        $source = null;
        foreach ($responses as $response) {
            if ($response->getMethodCallId() === $this->resultOf && $response->getName() === $this->name) {
                $source = $response;
                break;
            }
        }
        if (!$source) {
            throw new MethodInvocationException(
                "invalidResultReference",
                "methodCallId or name do not match any previous invocation"
            );
        }

        // 2. Evaluate the JSON Pointer
        try {
            $pointer = JsonPointer::fromString($this->path);
            return $pointer->evaluate($source->getArguments());
        } catch (\Exception $exception) {
            throw new MethodInvocationException("invalidResultReference", "Path could not be resolved");
        }
    }
}
