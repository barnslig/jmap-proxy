<?php

namespace JP\JMAP\RequestErrors;

use JP\JMAP\RequestError;
use JsonSchema\Exception\ValidationException;

class NotRequestError extends RequestError
{
    public function __construct(ValidationException $exception)
    {
        parent::__construct("urn:ietf:params:jmap:error:notRequest", 400, [
            "detail" => $exception->getMessage()
        ]);
    }
}
