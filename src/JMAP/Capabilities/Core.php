<?php

namespace JP\JMAP\Capabilities;

use JP\JMAP\Capability;

class Core extends Capability
{
    public function __construct()
    {
        parent::__construct();

        $this->addType("Core", new \JP\JMAP\Capabilities\Types\Core());
    }

    public function getCapabilities(): array
    {
        return [
            "maxSizeUpload" => 50000000,
            "maxConcurrentUpload" => 4,
            "maxSizeRequest" => 10000000,
            "maxConcurrentRequests" => 4,
            "maxCallsInRequest" => 16,
            "maxObjectsInGet" => 500,
            "maxObjectsInSet" => 500,
            "collationAlgorithms" => []
        ];
    }
}
