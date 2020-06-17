<?php

namespace barnslig\JMAP\Core\Exceptions;

use RuntimeException;

/**
 * Exception that is raised when an unknown capability is requested
 *
 * @see https://tools.ietf.org/html/rfc8620#section-3.6.1
 */
class UnknownCapabilityException extends RuntimeException
{
}
