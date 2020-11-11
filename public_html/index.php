<?php

declare(strict_types=1);

use Barnslig\Jmap\Core\Capabilities\CoreCapability;
use Barnslig\Jmap\Core\CapabilityFactory;
use Barnslig\Jmap\Core\Controllers\AbstractControllerFactory;
use Barnslig\Jmap\Core\Controllers\ApiController;
use Barnslig\Jmap\Core\Controllers\SessionController;
use Barnslig\Jmap\Core\RequestContext;
use Barnslig\Jmap\Core\RequestContextFactory;
use Barnslig\Jmap\Core\Schemas\ValidatorFactory;
use Barnslig\Jmap\Core\Schemas\ValidatorInterface;
use Barnslig\Jmap\Core\Session;
use Barnslig\Jmap\Core\SessionFactory;
use Barnslig\Jmap\Mail\MailCapability;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Laminas\ServiceManager\ServiceManager;

require_once __DIR__ . '/../vendor/autoload.php';


// SETUP JMAP
$container = new ServiceManager([
    'services' => [
        'config' => [
            'session' => [
                'capabilities' => [
                    CoreCapability::class,
                    MailCapability::class,
                ],
            ],

            CoreCapability::class => [
                'maxSizeUpload' => 50000000,
                'maxConcurrentUpload' => 4,
                'maxSizeRequest' => 10000000,
                'maxConcurrentRequests' => 4,
                'maxCallsInRequest' => 16,
                'maxObjectsInGet' => 500,
                'maxObjectsInSet' => 500,
                'collationAlgorithms' => [],
            ],

            MailCapability::class => [
                "maxMailboxesPerEmail" => null,
                "maxMailboxDepth" => null,
                "maxSizeMailboxName" => 100,
                "maxSizeAttachmentsPerEmail" => 50000000,
                "emailQuerySortOptions" => [],
                "mayCreateTopLevelMailbox" => true
            ],
        ],
    ],
    'factories' => [
        // JMAP Core
        RequestContext::class => RequestContextFactory::class,
        Session::class => SessionFactory::class,
        ValidatorInterface::class => ValidatorFactory::class,

        // JMAP Capabilities
        CoreCapability::class => CapabilityFactory::class,
        MailCapability::class => CapabilityFactory::class,

        // PSR-15 HTTP Controllers
        ApiController::class => AbstractControllerFactory::class,
        SessionController::class => AbstractControllerFactory::class,
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
