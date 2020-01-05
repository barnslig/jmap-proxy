<?php

namespace JP\JMAP\RequestErrors;

use JP\JMAP\RequestError;

class NotJsonError extends RequestError
{
    public function __construct()
    {
        parent::__construct("urn:ietf:params:jmap:error:notRequest", 400, [
            "detail" => "The content type of the request was not application/json or the request did not parse as I-JSON."
        ]);
    }
}
