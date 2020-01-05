<?php

namespace JP\JMAP\Capabilities\Types;

use JP\JMAP\Invocation;
use JP\JMAP\Session;
use JP\JMAP\Type;

class Core extends Type
{
    public function __construct()
    {
        parent::__construct();

        $this->addMethod('echo', [$this, 'echoHandler']);
    }

    public function echoHandler(Invocation $request, Session $session): Invocation
    {
        return $request;
    }
}
