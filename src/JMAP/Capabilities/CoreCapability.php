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

        $this->addType(new CoreCapability\CoreType(), [
            new CoreCapability\CoreType\CoreEchoMethod()
        ]);
    }

    public function getCapabilities(): object
    {
        return (object)$this->options;
    }

    public function getName(): string
    {
        return "urn:ietf:params:jmap:core";
    }
}
