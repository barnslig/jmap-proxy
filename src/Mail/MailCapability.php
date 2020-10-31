<?php

namespace barnslig\JMAP\Mail;

use barnslig\JMAP\Core\Capability;
use Ds\Map;

class MailCapability extends Capability
{
    /** @var array<string, mixed> */
    private $options;

    /**
     * @param array<string, mixed> $options
     */
    public function __construct($options = [])
    {
        $this->options = array_merge([
            "maxMailboxesPerEmail" => null,
            "maxMailboxDepth" => null,
            "maxSizeMailboxName" => 100,
            "maxSizeAttachmentsPerEmail" => 50000000,
            "emailQuerySortOptions" => [],
            "mayCreateTopLevelMailbox" => true
        ], $options);
    }

    public function getCapabilities(): object
    {
        return (object)$this->options;
    }

    public function getMethods(): Map
    {
        return new Map([
            "Mailbox/get" => MailCapability\MailboxType\MailboxGetMethod::class
        ]);
    }

    public function getName(): string
    {
        return "urn:ietf:params:jmap:mail";
    }
}
