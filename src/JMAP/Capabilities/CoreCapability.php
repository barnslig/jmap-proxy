<?php

namespace JP\JMAP\Capabilities;

use JP\JMAP\Capability;
use JP\JMAP\Helper;

class CoreCapability extends Capability
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

        $this->addType("Core", new CoreCapability\CoreType());
    }

    public function getCapabilities(): array
    {
        return $this->options;
    }
}
