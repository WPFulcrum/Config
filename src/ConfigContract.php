<?php

namespace Fulcrum\Config;

interface ConfigContract
{
    /**
     * Retrieves all of the runtime configuration parameters
     *
     * @since 3.0.0
     *
     * @return array
     */
    public function all();

    /**
     * Get the specified configuration value.
     *
     * @since 3.0.0
     *
     * @param  string $parameterKey
     * @param  mixed $default
     *
     * @return mixed
     */
    public function get($parameterKey, $default = null);

    /**
     * Determine if the given configuration value exists.
     *
     * @since 3.0.0
     *
     * @param  string $parameterKey
     *
     * @return bool
     */
    public function has($parameterKey);

    /**
     * Push a configuration in via the key
     *
     * @since 3.0.0
     *
     * @param string $parameterKey Key to be assigned, which also becomes the property
     * @param mixed $value Value to be assigned to the parameter key
     *
     * @return null
     */
    public function push($parameterKey, $value);
}
