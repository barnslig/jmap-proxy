<?php

namespace JP\JMAP\RequestErrors;

use JP\JMAP\RequestError;

/**
 * Limit Error
 *
 * The request was not processed as it would have exceeded one of the request
 * limits defined on the capability object, such as maxSizeRequest,
 * maxCallsInRequest, or maxConcurrentRequests.
 *
 * @see https://tools.ietf.org/html/rfc8620#section-3.6.1
 */
class LimitError extends RequestError
{
    public function __construct(string $detail, string $limit)
    {
        parent::__construct("urn:ietf:params:jmap:error:limit", 400, [
            "detail" => $detail,
            "limit" => $limit
        ]);
    }
}
