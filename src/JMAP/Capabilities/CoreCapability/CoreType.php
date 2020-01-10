<?php

namespace JP\JMAP\Capabilities\CoreCapability;

use JP\JMAP\Type;

class CoreType extends Type
{
    public function __construct()
    {
        parent::__construct();

        $this->addMethod('echo', new CoreType\CoreEchoMethod());
    }
}
