<?php

namespace barnslig\JMAP\Core\Controllers;

use barnslig\JMAP\Core\Capabilities\CoreCapability;
use barnslig\JMAP\Core\Controllers\AbstractController;
use barnslig\JMAP\Core\RequestContext;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class AbstractControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     * @return AbstractController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $context = $container->get(RequestContext::class);
        $config = $container->get("config")[CoreCapability::class];

        return new $requestedName($context, $config);
    }
}
