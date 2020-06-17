<?php

namespace barnslig\JMAP\Mail\MailCapability;

use barnslig\JMAP\Core\Type;

class MailboxType implements Type
{
    public function getName(): string
    {
        return "Mailbox";
    }
}
