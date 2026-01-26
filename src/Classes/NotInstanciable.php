<?php

namespace Time2Split\Help\Classes;

/**
 * Specifies that a class is not instanciable.
 * 
 * It throws an {@see \Error} if the constructor is called.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\class
 */
trait NotInstanciable
{
    /**
     * Throws an error if the constructor is called.
     * 
     * @internal
     * @throws \Error if the constructor is called.
     * @link https://www.php.net/manual/fr/class.error.php \Error
     */
    private final function __construct()
    {
        throw new \Error(__CLASS__ . " is not an instanciable class");
    }
}
