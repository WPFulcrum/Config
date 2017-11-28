<?php

namespace Fulcrum\Config;

use ArrayObject;
use InvalidArgumentException;
use RuntimeException;
use Fulcrum\Extender\Arr\DotArray;

class Config extends ArrayObject implements ConfigContract
{
    /**
     * Runtime Configuration Parameters
     *
     * @var array
     */
    protected $items = [];

    /***************************
     * Instantiate & Initialize
     **************************/

    /**
     * Create a new configuration repository.
     *
     * @since 3.0.0
     *
     * @param  string|array $config File path and filename to the config array; or it is the
     *                                  configuration array.
     * @param  string|array $defaults Specify a defaults array, which is then merged together
     *                                  with the initial config array before creating the object.
     */
    public function __construct($config, $defaults = '')
    {
        $this->items = $this->fetchParameters($config);
        $this->initDefaults($defaults);

        parent::__construct($this->items, ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * Initialize Default Configuration parameters & merge into the
     * $config parameters
     *
     * @since 3.0.0
     *
     * @param string|array $defaults
     * @return null
     */
    protected function initDefaults($defaults)
    {
        if (!$defaults) {
            return;
        }

        $defaults = $this->fetchParameters($defaults);
        $this->initDefaultsInConfigArray($defaults);
    }

    /**
     * Fetch the runtime parameters or defaults.
     *
     * @since 3.0.0
     *
     * @param string|array $locationOrArray Parameters location or array.
     *
     * @return array
     */
    protected function fetchParameters($locationOrArray)
    {
        if (is_array($locationOrArray)) {
            return $locationOrArray;
        }

        return $this->loadFile($locationOrArray);
    }

    /**
     * Initializing the Config with its Defaults
     *
     * @since 3.0.0
     *
     * @param array $defaults
     * @return null
     */
    protected function initDefaultsInConfigArray(array $defaults)
    {
        $this->items = array_replace_recursive($defaults, $this->items);
    }

    /***************************
     * Public Methods
     **************************/

    /**
     * Retrieves all of the runtime configuration parameters
     *
     * @since 3.0.0
     *
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Checks if the parameters exists.  Uses dot notation for multidimensional keys.
     *
     * @since 3.0.0
     *
     * @param  string $parameterKey Parameter key, specified in dot notation, i.e. key.key.key
     * @return bool
     */
    public function has($parameterKey)
    {
        return DotArray::has($this->items, $parameterKey);
    }

    /**
     * Get the specified configuration value.
     *
     * @since 3.0.0
     *
     * @param  string $parameterKey Parameter key, specified in dot notation, i.e. key.key.key
     * @param  mixed $default
     * @return mixed
     */
    public function get($parameterKey, $default = null)
    {
        return DotArray::get($this->items, $parameterKey, $default);
    }

    /**
     * Checks if the parameter key is a valid array, which means:
     *      1. Does it the key exists (which can be dot notation)
     *      2. If the value is an array
     *      3. Is the value empty, i.e. when $validIfNotEmpty is set
     *
     * @since 3.0.0
     *
     * @param string $dotNotationKeys
     * @param bool $validWhenEmpty
     * @return bool
     */
    public function isArray($dotNotationKeys, $validWhenEmpty = true)
    {
        $value = DotArray::get($this->items, $dotNotationKeys);

        // If it's not valid when empty, check it here.
        if (false === $validWhenEmpty && empty($value) ) {
            return false;
        }

        return DotArray::isArrayAccessible($value);
    }

    /**
     * Valid the Config.
     *
     * @since 3.0.0
     *
     * @return bool
     */
    public function isValid()
    {
        if ($this->validator) {
            return $this->validator->isValid($this);
        }

        return true;
    }

    /**
     * Push a configuration in via the key
     *
     * @since 3.0.0
     *
     * @param string $parameterKey Key to be assigned, which also becomes the property
     * @param mixed $value Value to be assigned to the parameter key
     * @return null
     */
    public function push($parameterKey, $value)
    {
        $this->items[$parameterKey] = $value;
        $this->offsetSet($parameterKey, $value);
    }

    /**
     * Merge a new array into this config
     *
     * @since 3.0.0
     *
     * @param array $array_to_merge
     * @return null
     */
    public function merge(array $array_to_merge)
    {
        $this->items = array_replace_recursive($this->items, $array_to_merge);

        array_walk($this->items, function($value, $parameterKey) {
            $this->offsetSet($parameterKey, $value);
        });
    }

    /**
     * Push a configuration in via the key
     *
     * @since 3.0.0
     *
     * @param array|string $parameterKey Key to be assigned, which also becomes the property
     * @param mixed $value Value to be assigned to the parameter key
     * @return null
     */
    public function set($parameterKey, $value)
    {
        $keys = is_array($parameterKey) ? $parameterKey : [$parameterKey => $value];

        foreach ($keys as $key => $value) {
            DotArray::set($this->items, $key, $value);
        }
    }

    /***************************
     * Helpers
     **************************/

    /**
     * Loads the config file
     *
     * @since 3.0.0
     *
     * @param string $configFile
     * @return string
     */
    protected function loadFile($configFile)
    {
        if ($this->isFileValid($configFile)) {
            return include $configFile;
        }
    }

    /**
     * Build the config file's full qualified path
     *
     * @since 3.0.0
     *
     * @param string $file
     *
     * @return bool
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function isFileValid($file)
    {
        if (!$file) {
            throw new InvalidArgumentException(__('A config filename must not be empty.', 'fulcrum'));
        }

        if (!is_readable($file)) {
            throw new RuntimeException(sprintf(
                '%s %s',
                __('The specified config file is not readable', 'fulcrum'),
                $file
            ));
        }

        return true;
    }

    /*************************
     * ArrayAccess methods.
     ************************/

    public function offsetExists($key)
    {
        return $this->has($key);
    }

    public function offsetGet($key)
    {
        return $this->get($key);
    }

    public function offsetSet($key, $value)
    {
        $this->set($key, $value);
    }

    public function offsetUnset($key)
    {
        $this->set($key, null);
    }
}
