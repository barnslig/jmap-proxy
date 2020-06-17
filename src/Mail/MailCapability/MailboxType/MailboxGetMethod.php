<?php

namespace barnslig\JMAP\Mail\MailCapability\MailboxType;

use barnslig\JMAP\Core\Invocation;
use barnslig\JMAP\Core\Method;
use barnslig\JMAP\Core\Methods\GetMethod;
use barnslig\JMAP\Core\Session;

class MailboxGetMethod extends GetMethod
{
    public function handle(Invocation $request, Session $session): Invocation
    {
        parent::handle($request, $session);

        return $request;
    }
}
