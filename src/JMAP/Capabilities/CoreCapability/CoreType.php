<?php

namespace JP\JMAP\Capabilities\CoreCapability;

use JP\JMAP\Type;

class CoreType implements Type
{
    public function getName(): string
    {
        return "Core";
    }
}
