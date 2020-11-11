<?php

namespace Barnslig\Jmap\Core\RequestErrors;

use Barnslig\Jmap\Core\RequestError;

/**
 * Not JSON Error
 *
 * The content type of the request was not "application/json" or the request
 * did not parse as I-JSON.
 *
 * @see https://tools.ietf.org/html/rfc8620#section-3.6.1
 */
class NotJsonError extends RequestError
{
    public function __construct()
    {
        parent::__construct("urn:ietf:params:jmap:error:notRequest", 400, [
            "detail" =>
                "The content type of the request was not application/json or the request did not parse as I-JSON."
        ]);
    }
}
