<?php

namespace barnslig\JMAP\Core\Schemas;

use barnslig\JMAP\Core\Schemas\Validator;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Opis\JsonSchema\Validator as OpisValidator;
use OpisErrorPresenter\Implementation\MessageFormatterFactory;
use OpisErrorPresenter\Implementation\PresentedValidationErrorFactory;
use OpisErrorPresenter\Implementation\Strategies\BestMatchError;
use OpisErrorPresenter\Implementation\ValidationErrorPresenter;
use Interop\Container\ContainerInterface;

class ValidatorFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     * @return Validator
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $loader = new DirLoader();
        $loader->registerPath(__DIR__ . "/schemas/", "http://jmap.io");

        $validator = new OpisValidator(null, $loader);

        $presenter = new ValidationErrorPresenter(
            new PresentedValidationErrorFactory(new MessageFormatterFactory()),
            new BestMatchError()
        );

        return new Validator($loader, $validator, $presenter);
    }
}
