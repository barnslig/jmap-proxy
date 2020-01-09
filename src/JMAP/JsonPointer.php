<?php

namespace JP\JMAP;

use Ds\Vector;

/**
 * JMAP ResultReference JSON Pointer implementation
 *
 * According to RFC 8620 (JMAP) Section 3.7, this class implements RFC 6901
 * with the following addition in "Evaluation" (see Section 4):
 *
 * If the currently referenced value is a JSON array, the reference token may
 * be exactly the single character *, making the new referenced value the
 * result of applying the rest of the JSON Pointer tokens to every item in the
 * array and returning the results in the same order in a new array.
 *
 * If the result of applying the rest of the pointer tokens to each item was
 * itself an array, the contents of this array are added to the output rather
 * than the array itself (i.e., the result is flattened from an array of arrays
 * to a single array).
 */
class JsonPointer
{
    /** @var Vector */
    private $path;

    /**
     * Create a new instance from a string
     *
     * @param string $pointer JSON Pointer path
     * @throws InvalidArgumentException When the first path item does not match the whole document
     * @return JsonPointer New instance evaluating this pointer
     */
    public static function fromString(string $pointer): JsonPointer
    {
        $new = new self();

        $pathItems = new Vector(explode("/", $pointer));
        $isUriFragment = $pathItems->first() === "#";

        // Make sure the first path item matches the whole document
        if ($pathItems->first() !== "" && $pathItems->first() !== "#") {
            throw new \InvalidArgumentException("Path does not start with whole document");
        }

        // Reset first item so URI fragments starting with #/ work
        $pathItems->set(0, "");

        // Unescape the path
        $new->path = $pathItems->map(function ($pathItem) use ($isUriFragment) {
            return str_replace(array('~1', '~0'), array('/', '~'), $isUriFragment ? urldecode($pathItem) : $pathItem);
        });

        return $new;
    }

    /**
     * Create a new instance from a path Vector
     *
     * @param Vector $path JSON Pointer Vector, as created by self::fromString
     * @return JsonPointer New instance evaluating this pointer
     */
    public static function fromPath(Vector $path): JsonPointer
    {
        $new = new self();
        $new->path = $path;
        return $new;
    }

    /**
     * Get the parsed Pointer path
     *
     * @return Vector Pointer path
     */
    public function getPath(): Vector
    {
        return $this->path;
    }

    /**
     * Get an element from an array/object by it's key
     *
     * @param mixed $obj Object that should be queried
     * @param string $key Key/Index of the element
     * @throws OutOfRangeException When the key/index is not found
     * @return mixed Object element
     */
    private static function get(&$obj, string $key)
    {
        if ($obj instanceof \stdClass) {
            if (!property_exists($obj, $key)) {
                throw new \OutOfRangeException("Failed to fetch key: '" . $key . "'");
            }
            return $obj->{$key};
        }

        if (!isset($obj[$key])) {
            throw new \OutOfRangeException("Failed to fetch key: '" . $key . "'");
        }

        return $obj[$key];
    }

    /**
     * Evaluate the JSON pointer onto an object
     *
     * @param mixed $data Decoded JSON object to apply the pointer onto
     * @return object Result at pointer location
     */
    public function evaluate($data)
    {
        $cur = $data;
        foreach ($this->getPath()->slice(1) as $i => $pathItem) {
            if ($pathItem === "*" && is_array($cur)) {
                // JMAP extension to JSON Pointer
                $remainingPath = (new Vector([""]))->merge($this->getPath()->slice($i + 2));
                $newPointer = self::fromPath($remainingPath);

                $acc = new Vector();
                foreach ($cur as $item) {
                    $ev = $newPointer->evaluate($item);
                    if ($ev instanceof Vector) {
                        $acc->push(...$ev);
                    } else {
                        $acc->push($ev);
                    }
                }

                $cur = $acc;
                break;
            }

            $cur = self::get($cur, $pathItem);
        }

        return $cur;
    }
}
