<?php

namespace barnslig\JMAP\Core;

use barnslig\JMAP\Core\Session;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class SessionFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     * @return Session
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config')['session'];

        $session = new Session();
        foreach ($config['capabilities'] as $capability) {
            $session->addCapability($container->get($capability));
        }

        return $session;
    }
}
