<?php

namespace JP\JMAP\RequestErrors;

use JP\JMAP\RequestError;

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
