<?php

namespace JP\JMAP;

final class Helper
{
    /**
     * Pick a subset from an associative array based on some keys
     *
     * @param array $array Source array to pick from
     * @param array $keys Keys that should be picked from the source array
     * @return array Array only with the specified keys
     */
    public static function arrayPick(array $array, array $keys): array
    {
        return array_intersect_key($array, array_flip($keys));
    }
}
