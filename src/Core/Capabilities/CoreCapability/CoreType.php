<?php

namespace barnslig\JMAP\Core\Capabilities\CoreCapability;

use barnslig\JMAP\Core\Type;

class CoreType implements Type
{
    public function getName(): string
    {
        return "Core";
    }
}
