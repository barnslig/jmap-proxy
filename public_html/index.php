<?php

declare(strict_types=1);

use barnslig\JMAP\Core\JMAP;
use barnslig\JMAP\Mail\MailCapability;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;

require_once __DIR__ . '/../vendor/autoload.php';

// SETUP JMAP
$jmap = new JMAP();
$jmap->getSession()->addCapability(new MailCapability());


// SETUP ROUTER
$router = new League\Route\Router();

$router->get('/.well-known/jmap', [$jmap, 'sessionHandler']);
$router->post('/jmap', [$jmap, 'apiHandler']);


// DISPATCH REQUEST
$request = ServerRequestFactory::fromGlobals();

try {
    $response = $router->dispatch($request);
    (new SapiEmitter())->emit($response);
} catch (\League\Route\Http\Exception\NotFoundException $exception) {
    // pass on so php -S can serve static files
    return false;
}
