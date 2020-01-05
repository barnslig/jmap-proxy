<?php

namespace JP\JMAP;

use Ds\Map;
use JsonSerializable;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

/**
 * RFC7807 error
 */
class RequestError implements JsonSerializable
{
    /** @var string */
    private $type;

    /** @var int */
    private $status = 400;

    /** @var array */
    private $error;

    public function __construct(string $type, int $status, array $error)
    {
        $this->type = $type;
        $this->status = $status;
        $this->error = $error;
    }

    public function asResponse(): ResponseInterface
    {
        return new JsonResponse($this, $this->getStatus(), [
            'Content-Type' => 'application/problem+json'
        ]);
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function jsonSerialize()
    {
        return (new Map([
            "type" => $this->type,
            "status" => $this->status
        ]))->merge($this->error);
    }
}
