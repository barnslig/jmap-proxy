<?php

namespace JP\JMAP;

use JsonSchema\Constraints\Constraint;
use JsonSchema\Validator;

class ResultReference {
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

    public function __construct()
    {

    }
}