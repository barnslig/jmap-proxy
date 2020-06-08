<?php

namespace JP\JMAP\Schemas;

use Ds\Map;
use Opis\JsonSchema\ValidationError as OpisValidationError;
use Opis\JsonSchema\ValidationResult as OpisValidationResult;
use Opis\JsonSchema\Validator as OpisValidator;
use OpisErrorPresenter\Implementation\MessageFormatterFactory;
use OpisErrorPresenter\Implementation\PresentedValidationErrorFactory;
use OpisErrorPresenter\Implementation\Strategies\BestMatchError;
use OpisErrorPresenter\Implementation\ValidationErrorPresenter;

/**
 * JSON Schema Validator for JMAP
 *
 * This schema validator automatically loads all schemas required for validating
 * JMAP requests, makes them available via URI and throws a human-readable
 * error message on validation failure.
 */
class Validator
{
    /** @var DirLoader */
    private $loader;

    /** @var OpisValidator */
    private $validator;

    /** @var ValidationErrorPresenter */
    private $presenter;

    public function __construct()
    {
        $this->loader = new DirLoader();
        $this->loader->registerPath(__DIR__ . "/schemas/", "http://jmap.io");

        $this->validator = new OpisValidator(null, $this->loader);

        $this->presenter = new ValidationErrorPresenter(
            new PresentedValidationErrorFactory(
                new MessageFormatterFactory(
                    new BestMatchError()
                )
            )
        );
    }

    /**
     * Validate data against a schema
     *
     * @param object $data Data that should be validated
     * @param string $uri URI of the JSON schema, e.g. http://jmap.io/Request.json#
     * @throws ValidationException When the validation fails
     * @return void
     */
    public function validate(object $data, string $uri): void
    {
        /* Convert Ds\Map structures so we can validate them.
         * This is necessary as, for example, Invocation is using Ds\Map to
         * store arguments which we commonly validate.
         */
        if ($data instanceof Map) {
            $data = (object)$data->toArray();
        }

        $result = $this->validator->uriValidation($data, $uri);

        if ($result->hasErrors()) {
            $presented = $this->presenter->present(...$result->getErrors())[0];
            $msg = "Error validating #/" . implode("/", $presented->pointer()) . ": " . $presented->message();
            throw new ValidationException($msg);
        }
    }
}
