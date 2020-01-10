<?php

namespace JP\JMAP;

abstract class Method
{
    abstract public function handle(Invocation $request, Session $session): Invocation;
}
