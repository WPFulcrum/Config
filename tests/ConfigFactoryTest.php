<?php

namespace Fulcrum\Config\Tests;

use Fulcrum\Config\Config;
use Fulcrum\Config\ConfigFactory;

class ConfigFactoryTest extends TestCase
{
    public function testCreateWhenGivenAnArray()
    {
        $this->assertInstanceOf(Config::class, ConfigFactory::create($this->testArray));

        $this->assertInstanceOf(Config::class, ConfigFactory::create($this->testArray, $this->defaults));
    }

    public function testCreateWhenGivenFilePath()
    {
        $this->assertInstanceOf(Config::class, ConfigFactory::create($this->testArrayPath));

        $this->assertInstanceOf(Config::class, ConfigFactory::create($this->testArrayPath, $this->defaultsPath));
    }
}
