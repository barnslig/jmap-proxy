<?php

namespace JP\JMAP\Schemas;

use Opis\JsonSchema\ISchemaLoader;
use Opis\JsonSchema\Schema;
use RuntimeException;

/**
 * JSON Schema loader that maps a directory to an URI
 *
 * @see https://docs.opis.io/json-schema/1.x/php-loader.html#creating-a-custom-loader
 */
class DirLoader implements ISchemaLoader
{
    /** @var string[] */
    protected $map = [];

    /** @var Schema[] */
    protected $loaded = [];

    /**
     * @inheritdoc
     */
    public function loadSchema(string $uri)
    {
        // Check if already loaded
        if (isset($this->loaded[$uri])) {
            return $this->loaded[$uri];
        }

        // Check the mapping
        foreach ($this->map as $prefix => $dir) {
            if (strpos($uri, $prefix) === 0) {
                // We have a match
                $path = substr($uri, strlen($prefix) + 1);
                $path = $dir . '/' . ltrim($path, '/');

                if (file_exists($path)) {
                    // Load the schema file
                    $rawSchema = file_get_contents($path);
                    if ($rawSchema === false) {
                        throw new RuntimeException("Cannot load schema " . $path);
                    }

                    // Create a schema object
                    $schema = Schema::fromJsonString($rawSchema);

                    // Save it for reuse
                    $this->loaded[$uri] = $schema;

                    return $schema;
                }
            }
        }

        // Nothing found
        return null;
    }

    /**
     * Map a URL prefix to a path prefix
     *
     * @param string $dir Path prefix, e.g. schemas/
     * @param string $uriPrefix URI prefix, e.g. http://example.com
     * @return bool Whether the path prefix is not a directory
     */
    public function registerPath(string $dir, string $uriPrefix): bool
    {
        if (!is_dir($dir)) {
            return false;
        }

        $uriPrefix = rtrim($uriPrefix, '/');
        $dir = rtrim($dir, '/');

        $this->map[$uriPrefix] = $dir;

        return true;
    }
}
