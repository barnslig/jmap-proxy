<?php

namespace Barnslig\Jmap\Mail\MailCapability\MailboxType;

use Barnslig\Jmap\Core\Invocation;
use Barnslig\Jmap\Core\Methods\GetMethod;
use Barnslig\Jmap\Core\RequestContext;

class MailboxGetMethod extends GetMethod
{
    public function handle(Invocation $request, RequestContext $context): Invocation
    {
        return parent::handle($request, $context);
    }
}
