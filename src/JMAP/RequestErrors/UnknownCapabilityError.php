<?php

namespace JP\JMAP\RequestErrors;

use JP\JMAP\RequestError;
use JP\JMAP\Exceptions\UnknownCapabilityException;

class UnknownCapabilityError extends RequestError
{
    public function __construct(UnknownCapabilityException $exception)
    {
        parent::__construct("urn:ietf:params:jmap:error:unknownCapability", 400, [
            "detail" => $exception->getMessage()
        ]);
    }
}
