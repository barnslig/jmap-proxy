<?php

namespace barnslig\JMAP\Core;

use Ds\Map;
use JsonSerializable;
use Laminas\Diactoros\Response\JsonResponse;

/**
 * Request-Level Error that returns a RFC 7807 object as an PSR-7 compatible HTTP response according to RFC 7807
 *
 * @see https://tools.ietf.org/html/rfc8620#section-3.6.1
 * @see https://tools.ietf.org/html/rfc7807
 * @see https://www.php-fig.org/psr/psr-7/#33-psrhttpmessageresponseinterface
 */
class RequestError extends JsonResponse implements JsonSerializable
{
    /** @var string */
    private $type;

    /** @var int */
    private $status = 400;

    /** @var array<mixed> */
    private $error;

    /**
     * Create a new request error
     *
     * @param string $type Error type, e.g. "urn:ietf:params:jmap:error:notJSON"
     * @param int $status HTTP error status, e.g. 400
     * @param array<string, mixed> $error Additional associative error data that is merged with type and status
     */
    public function __construct(string $type, int $status, array $error)
    {
        $this->type = $type;
        $this->status = $status;
        $this->error = $error;

        parent::__construct($this, $this->status, [
            'Content-Type' => 'application/problem+json'
        ]);
    }

    public function jsonSerialize()
    {
        return array_merge([
            "type" => $this->type,
            "status" => $this->status
        ], $this->error);
    }
}
