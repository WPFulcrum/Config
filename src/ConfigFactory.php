<?php

namespace Fulcrum\Config;

class ConfigFactory
{
    /**
     * Load and return the Config object
     *
     * @since 3.0.0
     *
     * @param  string|array $config File path and filename to the config array; or it is the
     *                                  configuration array.
     * @param  string|array $defaults Specify a defaults array, which is then merged together
     *                                  with the initial config array before creating the object.
     * @returns Fulcrum Returns the Config object
     */
    public static function create($config, $defaults = '')
    {
        return new Repository($config, $defaults);
    }
}
