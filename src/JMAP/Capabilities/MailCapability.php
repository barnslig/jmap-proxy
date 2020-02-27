<?php

namespace JP\JMAP\Capabilities;

use JP\JMAP\Capability;

class MailCapability extends Capability
{
    public function __construct()
    {
        parent::__construct();

        // $this->addType("Mailbox", null);
        // $this->addType("Thread", null);
        // $this->addType("Email", null);
        // $this->addType("SearchSnippet", null);
    }

    public function getCapabilities(): object
    {
        return (object)[
            "maxMailboxesPerEmail" => null,
            "maxMailboxDepth" => null,
            "maxSizeMailboxName" => 100,
            "maxSizeAttachmentsPerEmail" => 50000000,
            "emailQuerySortOptions" => [],
            "mayCreateTopLevelMailbox" => true
        ];
    }

    public function getName(): string
    {
        return "urn:ietf:params:jmap:mail";
    }
}
