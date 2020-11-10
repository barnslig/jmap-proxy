<?php

namespace Barnslig\Jmap\Core;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Barnslig\Jmap\Core\Capability;

class CapabilityFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     * @return Capability
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get("config");

        $capabilityConfig = array_key_exists($requestedName, $config)
            ? $config[$requestedName]
            : [];

        return new $requestedName($capabilityConfig);
    }
}
