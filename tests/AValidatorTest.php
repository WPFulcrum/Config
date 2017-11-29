<?php

namespace Fulcrum\Config\Tests;

use Brain\Monkey\Functions;
use Fulcrum\Config\Exception\InvalidConfigException;
use Fulcrum\Config\Exception\InvalidFileException;
use Fulcrum\Config\Exception\InvalidSourceException;
use Fulcrum\Config\Validator;

class AValidatorTest extends TestCase
{
    public function testThrowsErrorWhenSourceIsInvalid()
    {
        $errorMessage = 'Invalid configuration source. Source must be an array of configuration parameters or a string filesystem path to load the configuration parameters.'; // @codingStandardsIgnoreLine - Generic.Files.LineLength.TooLong
        Functions\when('__')
            ->justEcho($errorMessage);

        $this->expectException(InvalidSourceException::class);
        $this->expectOutputString($errorMessage);
        Validator::mustBeStringOrArray(null);

        $this->expectException(InvalidSourceException::class);
        $this->expectOutputString($errorMessage);
        Validator::mustBeStringOrArray(new \stdClass);

        $this->expectException(InvalidSourceException::class);
        $this->expectOutputString($errorMessage);
        Validator::mustBeStringOrArray(10);

        // Check the defaults too.
        $errorMessage = 'Invalid default configuration source. Source must be an array of default configuration parameters or a string filesystem path to load the default configuration parameters.'; // @codingStandardsIgnoreLine - Generic.Files.LineLength.TooLong
        $this->expectException(InvalidSourceException::class);
        $this->expectOutputString($errorMessage);
        Validator::mustBeStringOrArray($this->testArray, new \stdClass);

        $this->expectException(InvalidSourceException::class);
        $this->expectOutputString($errorMessage);
        Validator::mustBeStringOrArray($this->testArray, 10);
    }

    public function testReturnsTrueWhenSourceIsValid()
    {
        $this->assertTrue(Validator::mustBeStringOrArray($this->testArray));
        $this->assertTrue(Validator::mustBeStringOrArray($this->defaults));

        $this->assertTrue(Validator::mustBeStringOrArray($this->testArrayPath));
        $this->assertTrue(Validator::mustBeStringOrArray($this->defaultsPath));
    }

    public function testThrowsErrorWhenNotAnArray()
    {
        $errorMessage = 'Invalid configuration. The configuration must an array.';
        Functions\when('__')
            ->justEcho($errorMessage);

        $this->expectException(InvalidConfigException::class);
        $this->expectOutputString($errorMessage);
        Validator::mustBeAnArray(require FULCRUM_CONFIG_TESTS_DIR . '/fixtures/invalid-config.php');
    }

    public function testReturnsTrueWhenAnArray()
    {
        $this->assertTrue(Validator::mustBeAnArray($this->testArray));
        $this->assertTrue(Validator::mustBeAnArray($this->defaults));

        $this->assertTrue(Validator::mustBeAnArray(require $this->testArrayPath));
        $this->assertTrue(Validator::mustBeAnArray(require $this->defaultsPath));
    }

    public function testThrowsErrorWhenSourceIsEmpty()
    {
        $errorMessage = 'Empty configuration source error.  The configuration source cannot be empty.';
        Functions\when('__')
            ->justEcho($errorMessage);
        $this->expectException(InvalidSourceException::class);
        $this->expectOutputString($errorMessage);
        Validator::mustNotBeEmpty([]);

        $this->expectException(InvalidSourceException::class);
        $this->expectOutputString($errorMessage);
        Validator::mustNotBeEmpty('');
    }

    public function testReturnsTrueWhenSourceIsNotEmpty()
    {
        $this->assertTrue(Validator::mustNotBeEmpty($this->testArray));
        $this->assertTrue(Validator::mustNotBeEmpty($this->defaults));

        $this->assertTrue(Validator::mustNotBeEmpty($this->testArrayPath));
        $this->assertTrue(Validator::mustNotBeEmpty($this->defaultsPath));

        // These are not configurations, but they are also not empty.
        $this->assertTrue(Validator::mustNotBeEmpty('foo'));
        $this->assertTrue(Validator::mustNotBeEmpty(10));
        $this->assertTrue(Validator::mustNotBeEmpty(new \stdClass()));
    }

    public function testThrowsErrorWhenFileIsInvalid()
    {
        $file         = 'file-does-not-exist.php';
        $errorMessage = 'The specified configuration file is not readable: ' . $file;
        Functions\when('__')
            ->justEcho($errorMessage);

        $this->expectException(InvalidFileException::class);
        $this->expectOutputString($errorMessage);
        Validator::mustBeLoadable($file);
    }

    public function testReturnsTrueWhenFileIsValid()
    {
        $this->assertTrue(Validator::mustBeLoadable($this->testArrayPath));
        $this->assertTrue(Validator::mustBeLoadable($this->defaultsPath));
        $this->assertTrue(Validator::mustBeLoadable(__FILE__));
    }
}
