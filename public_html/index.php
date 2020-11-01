<?php

declare(strict_types=1);

use barnslig\JMAP\Core\Capabilities\CoreCapability;
use barnslig\JMAP\Core\Controllers\ApiController;
use barnslig\JMAP\Core\Controllers\SessionController;
use barnslig\JMAP\Core\RequestContext;
use barnslig\JMAP\Core\Schemas\ValidatorFactory;
use barnslig\JMAP\Core\Schemas\ValidatorInterface;
use barnslig\JMAP\Core\Session;
use barnslig\JMAP\Core\SessionFactory;
use barnslig\JMAP\Mail\MailCapability;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;
use Laminas\ServiceManager\ServiceManager;

require_once __DIR__ . '/../vendor/autoload.php';


// SETUP JMAP
$container = new ServiceManager([
    'abstract_factories' => [
        ReflectionBasedAbstractFactory::class,
    ],
    'services' => [
        'config' => [
            'maxSizeUpload' => 50000000,
            'maxConcurrentUpload' => 4,
            'maxSizeRequest' => 10000000,
            'maxConcurrentRequests' => 4,
            'maxCallsInRequest' => 16,
            'maxObjectsInGet' => 500,
            'maxObjectsInSet' => 500,
            'collationAlgorithms' => [],

            'session' => [
                'capabilities' => [
                    CoreCapability::class,
                    MailCapability::class,
                ],
            ],
        ],
    ],
    'factories' => [
        // JMAP Core
        RequestContext::class => ReflectionBasedAbstractFactory::class,
        Session::class => SessionFactory::class,
        ValidatorInterface::class => ValidatorFactory::class,

        // JMAP Capabilities
        CoreCapability::class => ReflectionBasedAbstractFactory::class,
        MailCapability::class => ReflectionBasedAbstractFactory::class,

        // PSR-15 HTTP Controllers
        ApiController::class => ReflectionBasedAbstractFactory::class,
        SessionController::class => ReflectionBasedAbstractFactory::class,
    ]
]);


// SETUP ROUTER
$router = new League\Route\Router();

$router->get('/.well-known/jmap', [$container->get(SessionController::class), 'handle']);
$router->post('/jmap', [$container->get(ApiController::class), 'handle']);


// DISPATCH REQUEST
$request = ServerRequestFactory::fromGlobals();

try {
    $response = $router->dispatch($request);
    (new SapiEmitter())->emit($response);
} catch (\League\Route\Http\Exception\NotFoundException $exception) {
    // pass on so php -S can serve static files
    return false;
}
