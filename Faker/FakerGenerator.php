<?php

namespace Trappar\AliceGenerator\Faker;

class FakerGenerator
{
    public static function generate($fakerName, array $arguments)
    {
        $arguments = self::handleArray($arguments);

        return "<$fakerName($arguments)>";
    }

    private static function handleType($value)
    {
        switch (gettype($value)) {
            case 'array':
                return '[' . self::handleArray($value) . ']';
            case 'string':
                return '"' . $value . '"';
            case 'boolean':
                return ($value) ? 'true' : 'false';
            case 'NULL':
                return 'null';
            default:
                return $value;
        }
    }

    private static function handleArray(array $array)
    {
        return implode(', ', array_map(['self', 'handleType'], $array));
    }
}