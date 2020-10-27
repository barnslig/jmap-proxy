<?php

namespace barnslig\JMAP\Core\Schemas;

interface ValidatorInterface
{
    /**
     * Validate data against a schema
     *
     * @param object|Map $data Data that should be validated
     * @param string $uri URI of the JSON schema, e.g. http://jmap.io/Request.json#
     * @throws ValidationException When the validation fails
     * @return void
     */
    public function validate($data, string $uri): void;
}
