<?php

namespace JP\JMAP\Capabilities\MailCapability\MailboxType;

use JP\JMAP\Invocation;
use JP\JMAP\Method;
use JP\JMAP\Methods\GetMethod;
use JP\JMAP\Session;

class MailboxGetMethod extends GetMethod
{
    public function handle(Invocation $request, Session $session): Invocation
    {
        parent::handle($request, $session);

        return $request;
    }
}
