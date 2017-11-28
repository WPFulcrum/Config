<?php

namespace Fulcrum\Config\Tests;

use Fulcrum\Config\Config;

class ConfigWhenFilePathGivenTest extends \PHPUnit_Framework_TestCase
{
    protected $isLoaded = false;
    protected $fooFile;
    protected $defaultsFile;

    protected function setUp()
    {
        parent::setUp();

        if (!$this->isLoaded) {
            $this->fooFile      = __DIR__ . '/fixtures/foo.php';
            $this->defaultsFile = __DIR__ . '/fixtures/foo-defaults.php';
            $this->isLoaded     = true;
        }
    }

    public function testCreate()
    {
        $this->assertTrue(true);
//        $config = new Config(self::$testArray);
//        $this->assertInstanceOf(Config::class, $config);
//
//        $config = new Config(self::$testArray, self::$defaultsArray);
//        $this->assertInstanceOf(Config::class, $config);
    }
}
