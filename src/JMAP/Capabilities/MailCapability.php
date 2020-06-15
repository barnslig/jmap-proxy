<?php

namespace JP\JMAP\Capabilities;

use JP\JMAP\Capability;

class MailCapability extends Capability
{
    /** @var array<string, mixed> */
    private $options;

    /**
     * @param array<string, mixed> $options
     */
    public function __construct($options = [])
    {
        parent::__construct();

        $this->options = array_merge([
            "maxMailboxesPerEmail" => null,
            "maxMailboxDepth" => null,
            "maxSizeMailboxName" => 100,
            "maxSizeAttachmentsPerEmail" => 50000000,
            "emailQuerySortOptions" => [],
            "mayCreateTopLevelMailbox" => true
        ], $options);

        $this->addType(new MailCapability\MailboxType(), [
            new MailCapability\MailboxType\MailboxGetMethod()
        ]);
    }

    public function getCapabilities(): object
    {
        return (object)$this->options;
    }

    public function getName(): string
    {
        return "urn:ietf:params:jmap:mail";
    }
}
