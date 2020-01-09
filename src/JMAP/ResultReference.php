<?php

namespace JP\JMAP;

use Ds\Vector;
use JP\JMAP\Exceptions\MethodInvocationException;
use JsonSchema\Constraints\Constraint;
use JsonSchema\Exception\ValidationException;
use JsonSchema\Validator;

class ResultReference
{
    /** @var array */
    private static $schema = [
        "type" => "object",
        "properties" => [
            "resultOf" => [
                "type" => "string",
                "required" => true
            ],
            "name" => [
                "type" => "string",
                "required" => true
            ],
            "path" => [
                "type" => "string",
                "required" => true
            ]
        ]
    ];

    /** @var string */
    private $resultOf;

    /** @var string */
    private $name;

    /** @var string */
    private $path;

    /**
     * Construct a new ResultReference
     *
     * @param object $data Parsed result reference JSON
     * @throws MethodInvocationException When the data cannot be validated
     */
    public function __construct(object $data)
    {
        $validator = new Validator();
        try {
            $validator->validate($data, static::$schema, Constraint::CHECK_MODE_EXCEPTIONS);
        } catch (ValidationException $exception) {
            throw new MethodInvocationException("invalidResultReference", $exception->getMessage());
        }

        $this->resultOf = $data->resultOf;
        $this->name = $data->name;
        $this->path = $data->path;
    }

    /**
     * Resolve the reference from a Vector of response Invocations
     *
     * @param Vector $responses Vector of already computed response Invocations
     * @throws MethodInvocationException When there is not matching response for the methodCallId/name combination
     * @throws MethodInvocationException When the path could not be resolved
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
            throw new MethodInvocationException("invalidResultReference", "methodCallId or name do not match any previous invocation");
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
