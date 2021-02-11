<?php

declare(strict_types=1);

namespace Pollen\WpApp\Support;

use Illuminate\Support\Arr as BaseArr;
use Pollen\WpApp\Validation\Validator as v;

class Arr extends BaseArr
{
    /**
     * Serialisation de données si nécessaire.
     * @see https://codex.wordpress.org/Function_Reference/maybe_serialize
     *
     * @param string|array|object $data .
     *
     * @return mixed
     */
    public static function serialize($data)
    {
        if (is_array($data) || is_object($data)) {
            $data = serialize($data);
        } elseif (v::serialized(false)->validate($data)) {
            $data = serialize($data);
        }

        return $data;
    }

    /**
     * Suppression des slashes.
     *
     * @param string|array|object $data
     *
     * @return string|array|object
     */
    public static function stripslashes($data)
    {
        if (is_array($data)) {
            foreach ($data as $index => $item) {
                $data[$index] = static::stripslashes($item);
            }
        } elseif (is_object($data)) {
            $object_vars = get_object_vars($data);

            foreach ($object_vars as $property_name => $property_value) {
                $data->$property_name = static::stripslashes($property_value);
            }
        } elseif (is_string($data)) {
            $data = stripslashes($data);
        }

        return $data;
    }

    /**
     * Insertion d'un tableau après une clé d'indice
     * @see https://gist.github.com/wpscholar/0deadce1bbfa4adb4e4c
     *
     * @param array $array
     * @param mixed $new
     * @param string|int $key
     *
     * @return array
     */
    public static function insertAfter(array $array, $new, $key): array
    {
        $keys = array_keys($array);
        $index = array_search($key, $keys);
        $pos = false === $index ? count($array) : $index;

        return array_merge(
            array_slice($array, 0, $pos, true),
            [$pos => $new],
            array_slice($array, $pos, null, true)
        );
    }
}