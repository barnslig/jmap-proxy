<?php

namespace barnslig\JMAP\Core;

use barnslig\JMAP\Core\RequestContext;
use barnslig\JMAP\Core\Schemas\ValidatorInterface;
use barnslig\JMAP\Core\Session;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class RequestContextFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     * @return RequestContext
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $session = $container->get(Session::class);
        $validator = $container->get(ValidatorInterface::class);

        return new RequestContext($session, $validator);
    }
}
