<?php

namespace Barnslig\Jmap\Core\Controllers;

use Barnslig\Jmap\Core\Capabilities\CoreCapability;
use Barnslig\Jmap\Core\Controllers\AbstractController;
use Barnslig\Jmap\Core\RequestContext;
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
