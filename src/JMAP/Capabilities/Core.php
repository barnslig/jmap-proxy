<?php

namespace JP\JMAP\Capabilities;

use JP\JMAP\Capability;
use JP\JMAP\Helper;

class Core extends Capability
{
    /** @var array */
    private $options;

    public function __construct($options = [])
    {
        parent::__construct();

        $this->options = Helper::arrayPick($options, [
            "maxSizeUpload",
            "maxConcurrentUpload",
            "maxSizeRequest",
            "maxConcurrentRequests",
            "maxCallsInRequest",
            "maxObjectsInGet",
            "maxObjectsInSet",
            "collationAlgorithms"
        ]);

        $this->addType("Core", new \JP\JMAP\Capabilities\Types\Core());
    }

    public function getCapabilities(): array
    {
        return $this->options;
    }
}
