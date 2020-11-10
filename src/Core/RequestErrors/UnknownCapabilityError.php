<?php

namespace Barnslig\Jmap\Core\RequestErrors;

use Barnslig\Jmap\Core\RequestError;
use Barnslig\Jmap\Core\Exceptions\UnknownCapabilityException;

/**
 * Unknown Capability Error
 *
 * The client included a capability in the "using" property of the request
 * that the server does not support.
 *
 * @see https://tools.ietf.org/html/rfc8620#section-3.6.1
 */
class UnknownCapabilityError extends RequestError
{
    public function __construct(UnknownCapabilityException $exception)
    {
        parent::__construct("urn:ietf:params:jmap:error:unknownCapability", 400, [
            "detail" => $exception->getMessage()
        ]);
    }
}
