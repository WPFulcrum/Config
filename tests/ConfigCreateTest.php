<?php

namespace Fulcrum\Config\Tests;

use Brain\Monkey\Functions;
use Fulcrum\Config\Config;
use Fulcrum\Config\Exception\InvalidConfigException;
use Fulcrum\Config\Exception\InvalidFileException;
use Fulcrum\Config\Exception\InvalidSourceException;

class ConfigCreateTest extends TestCase
{
    public function testCreateWhenGivenAnArray()
    {
        $config = new Config($this->testArray);
        $this->assertInstanceOf(Config::class, $config);

        $config = new Config($this->testArray, $this->defaults);
        $this->assertInstanceOf(Config::class, $config);
    }

    public function testDefaultsAreOverwritten()
    {
        $config = new Config($this->testArray, $this->defaults);

        $this->assertEquals('WordPress', $config->foo['platform']);
        $this->assertEquals('Beans', $config->foo['theme']);
        $this->assertEquals('Tonya', $config->bar['baz']['who']);
    }

    public function testCreateWhenGivenFilePath()
    {
        $config = new Config($this->testArrayPath);
        $this->assertInstanceOf(Config::class, $config);

        $config = new Config($this->testArrayPath, $this->defaultsPath);
        $this->assertInstanceOf(Config::class, $config);
    }

    public function testThrowsErrorWhenSourceIsInvalid()
    {
        $errorMessage = 'Invalid configuration source. Source must be an array of configuration parameters or a string filesystem path to load the configuration parameters.';  // @codingStandardsIgnoreLine - Generic.Files.LineLength.TooLong
        Functions\when('__')
            ->justEcho($errorMessage);

        $this->expectException(InvalidSourceException::class);
        $this->expectOutputString($errorMessage);
        new Config(null);

        $this->expectException(InvalidSourceException::class);
        $this->expectOutputString($errorMessage);
        new Config(new \stdClass);

        $this->expectException(InvalidSourceException::class);
        $this->expectOutputString($errorMessage);
        new Config(10);

        // Check the defaults too.
        $errorMessage = 'Invalid default configuration source. Source must be an array of default configuration parameters or a string filesystem path to load the default configuration parameters.';  // @codingStandardsIgnoreLine - Generic.Files.LineLength.TooLong
        $this->expectException(InvalidSourceException::class);
        $this->expectOutputString($errorMessage);
        new Config($this->testArray, new \stdClass);

        $this->expectException(InvalidSourceException::class);
        $this->expectOutputString($errorMessage);
        new Config($this->testArray, 10);
    }

    public function testThrowsErrorWhenSourceIsEmpty()
    {
        $errorMessage = 'Empty configuration source error.';
        Functions\when('__')
            ->justEcho($errorMessage);
        $this->expectException(InvalidSourceException::class);
        $this->expectOutputString($errorMessage);
        new Config([]);

        $this->expectException(InvalidSourceException::class);
        $this->expectOutputString($errorMessage);
        new Config('');
    }

    public function testThrowsErrorWhenFileIsInvalid()
    {
        $file         = 'file-does-not-exist.php';
        $errorMessage = 'The specified configuration file is not readable: ' . $file;
        Functions\when('__')
            ->justEcho($errorMessage);

        $this->expectException(InvalidFileException::class);
        $this->expectOutputString($errorMessage);
        new Config($file);
    }

    public function testThrowsErrorWhenLoadedConfigIsInvalid()
    {
        $file         = FULCRUM_CONFIG_TESTS_DIR . '/fixtures/invalid-config.php';
        $errorMessage = 'Invalid configuration. The configuration must an array.';
        Functions\when('__')
            ->justEcho($errorMessage);

        $this->expectException(InvalidConfigException::class);
        $this->expectOutputString($errorMessage);
        new Config($file);

        $this->expectException(InvalidConfigException::class);
        $this->expectOutputString($errorMessage);
        new Config($this->testArray, $file);
    }
}
