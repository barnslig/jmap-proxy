<?php

namespace JP\JMAP\Capabilities;

use JP\JMAP\Capability;

class CoreCapability extends Capability
{
    /** @var array<string, mixed> */
    private $options;

    /**
     * @param array<string, mixed> $options
     */
    public function __construct($options = [])
    {
        parent::__construct();

        $this->options = $options;

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
