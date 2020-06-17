<?php

namespace barnslig\JMAP\Core\RequestErrors;

use barnslig\JMAP\Core\RequestError;
use barnslig\JMAP\Core\Schemas\ValidationException;

/**
 * Not Request Error
 *
 * The request parsed as JSON but did not match the type signature of the
 * Request object.
 *
 * @see https://tools.ietf.org/html/rfc8620#section-3.6.1
 */
class NotRequestError extends RequestError
{
    public function __construct(ValidationException $exception)
    {
        parent::__construct("urn:ietf:params:jmap:error:notRequest", 400, [
            "detail" => $exception->getMessage()
        ]);
    }
}