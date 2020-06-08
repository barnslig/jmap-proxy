<?php

namespace JP\JMAP\Capabilities\MailCapability;

use JP\JMAP\Type;

class MailboxType implements Type
{
    public function getName(): string
    {
        return "Mailbox";
    }
}
