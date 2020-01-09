<?php
declare(strict_types=1);

use JP\JMAP\JMAP;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;

require_once __DIR__ . '/../vendor/autoload.php';

// SETUP JMAP
$jmap = new JMAP();
$jmap->getSession()->addCapability('urn:ietf:params:jmap:mail', new JP\JMAP\Capabilities\Mail);


// SETUP ROUTER
$router = new League\Route\Router;

$router->get('/.well-known/jmap', [$jmap, 'sessionHandler']);
$router->post('/jmap', [$jmap, 'apiHandler']);


// DISPATCH REQUEST
$request = ServerRequestFactory::fromGlobals();
$response = $router->dispatch($request);

(new SapiEmitter())->emit($response);