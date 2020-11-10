<?php

namespace barnslig\JMAP\Mail\MailCapability\MailboxType;

use barnslig\JMAP\Core\Invocation;
use barnslig\JMAP\Core\Methods\GetMethod;
use barnslig\JMAP\Core\RequestContext;

class MailboxGetMethod extends GetMethod
{
    public function handle(Invocation $request, RequestContext $context): Invocation
    {
        return parent::handle($request, $context);
    }
}
