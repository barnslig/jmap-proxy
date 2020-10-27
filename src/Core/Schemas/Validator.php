<?php

namespace barnslig\JMAP\Core\Schemas;

use Ds\Map;
use Opis\JsonSchema\ISchemaLoader;
use Opis\JsonSchema\IValidator;
use OpisErrorPresenter\Contracts\ValidationErrorPresenter;

/**
 * JSON Schema Validator for JMAP
 *
 * This schema validator automatically loads all schemas required for validating
 * JMAP requests, makes them available via URI and throws a human-readable
 * error message on validation failure.
 */
class Validator implements ValidatorInterface
{
    /** @var ISchemaLoader */
    private $loader;

    /** @var IValidator */
    private $validator;

    /** @var ValidationErrorPresenter */
    private $presenter;

    public function __construct(ISchemaLoader $loader, IValidator $validator, ValidationErrorPresenter $presenter)
    {
        $this->loader = $loader;
        $this->validator = $validator;
        $this->presenter = $presenter;
    }

    public function validate($data, string $uri): void
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
