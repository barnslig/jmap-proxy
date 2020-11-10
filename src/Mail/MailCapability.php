<?php

namespace Barnslig\Jmap\Mail;

use Barnslig\Jmap\Core\Capability;
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
        $this->options = $options;
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
