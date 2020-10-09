<?php

namespace barnslig\JMAP\Core;

use Ds\Map;
use JsonSerializable;

/**
 * Abstract class to implement a JMAP capability
 *
 * Capabilities are used to extend the functionality of a JMAP server.
 * It consists of Types that provide Methods. For example, a capability
 * of type "urn:ietf:params:jmap:mail" may have a type called "Mailbox" with
 * a corresponding method called "get".
 *
 * Each capability is attached to a Session.
 */
abstract class Capability implements JsonSerializable
{
    /**
     * Get further information about this capability
     *
     * The return value is used within the JMAP Session Resource to give
     * further information about the server's capabilities in relation to
     * this capability.
     *
     * @see https://tools.ietf.org/html/rfc8620#section-2
     * @return object
     */
    abstract public function getCapabilities(): object;

    /**
     * Get Map of methods provided by this capability
     *
     * @return Map<string, Method>
     */
    abstract public function getMethods(): Map;

    /**
     * Get the capability identifier
     *
     * @return string Capability identifier, e.g. urn:ietf:params:jmap:core
     */
    abstract public function getName(): string;

    public function jsonSerialize()
    {
        return $this->getCapabilities();
    }
}
