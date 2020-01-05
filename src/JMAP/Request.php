<?php

namespace JP\JMAP;

use Ds\Map;
use Ds\Vector;
use JP\JMAP\Invocation;
use JsonSchema\Constraints\Constraint;
use JsonSchema\Validator;

class Request
{
    /** @var array */
    private static $schema = [
        "definitions" => [
            "Invocation" => [
                "type" => "array",
                "items" => [
                    [
                        "type" => "string",
                        "required" => "true"
                    ],
                    [
                        "type" => "object",
                        "required" => "true"
                    ],
                    [
                        "type" => "string",
                        "required" => "true"
                    ]
                ]
            ]
        ],
        "type" => "object",
        "properties" => [
            "using" => [
                "type" => "array",
                "required" => true,
                "items" => [
                    "type" => "string"
                ]
            ],
            "methodCalls" => [
                "type" => "array",
                "required" => "true",
                "items" => [
                    "\$ref" => "#/definitions/Invocation"
                ]
            ],
            "createdIds" => [
                "type" => "object"
            ]
        ]
    ];

    /**
     * Capability identifiers, the Vector consists of strings
     *
     * @var Vector
     */
    private $using;
    
    /**
     * Method calls, the Vector consists of Invocation instances
     *
     * @var Vector<Invocation>
     */
    private $methodCalls;

    /**
     * Object ID mappings, the Map consists of string keys and values
     *
     * @var Map
     */
    private $createdIds;

    /**
     * Construct a new Request instance
     *
     * During construction the JSON request body gets validated against a
     * JSON schema and method calls are mapped into Invocation instances.
     *
     * @param object $data Parsed JSON request body
     */
    public function __construct(object $data)
    {
        $validator = new Validator();
        $validator->validate($data, Request::$schema, Constraint::CHECK_MODE_EXCEPTIONS);

        $this->using = new Vector($data->using);
        // Sort the capability identifiers to canonicalize them for e.g. caching
        $this->using->sort();

        // Turn method calls into Invocation instances
        $this->methodCalls = (new Vector($data->methodCalls))->map(function ($methodCall) {
            return new Invocation(...$methodCall);
        });

        $this->createdIds = new Map(isset($data->createdIds) ? $parsedBody->createdIds : []);
    }

    public function getUsedCapabilities(): Vector
    {
        return $this->using;
    }

    public function getMethodCalls(): Vector
    {
        return $this->methodCalls;
    }

    public function getCreatedIds(): Map
    {
        return $this->createdIds;
    }
}
