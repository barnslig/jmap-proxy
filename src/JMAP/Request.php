<?php

namespace JP\JMAP;

use Ds\Map;
use Ds\Vector;
use JsonSchema\Constraints\Constraint;
use JsonSchema\Validator;

/**
 * JMAP request based on a JSON object that needs to be validated
 *
 * @see https://tools.ietf.org/html/rfc8620#section-3.3
 */
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
     * @param int $maxCallsInRequest Maximum calls a request may have
     * @throws OverflowException When maxCallsInRequest gets exceeded
     */
    public function __construct(object $data, int $maxCallsInRequest)
    {
        $validator = new Validator();
        $validator->validate($data, self::$schema, Constraint::CHECK_MODE_EXCEPTIONS);

        $this->using = new Vector($data->using);
        // Sort the capability identifiers to canonicalize them for e.g. caching
        $this->using->sort();

        if (count($data->methodCalls) > $maxCallsInRequest) {
            throw new \OverflowException("The maximum number of " . $maxCallsInRequest . " method calls got exceeded.");
        }

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
