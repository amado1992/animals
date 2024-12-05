<?php

namespace App\Enums;

class Enum
{
    /**
     * returns all constants of the enum
     *
     * @return array
     */
    public static function get()
    {
        $class = new \ReflectionClass(get_called_class());

        return $class->getConstants();
    }

    /**
     * returns the id of a given value
     *
     * @return array
     */
    public static function getKeys()
    {
        $class = new \ReflectionClass(get_called_class());

        return array_keys($class->getConstants());
    }

    /**
     * returns the index of a given value
     *
     * @return mixed
     */
    public static function getIndex($value)
    {
        $class = new \ReflectionClass(get_called_class());

        return array_search($value, $class->getConstants());
    }

    /**
     * returns the value of a given index
     *
     * @param string $value
     * @return int Id
     */
    public static function getValue($index)
    {
        $class = new \ReflectionClass(get_called_class());

        return $class->getConstants()[$index];
    }
}
