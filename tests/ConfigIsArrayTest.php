<?php

namespace Fulcrum\Config\Tests;

use Fulcrum\Config\Config;

class ConfigIsArrayTest extends TestCase
{
    public function testIsArray()
    {
        $config = new Config($this->testArray, $this->defaults);

        $this->assertTrue($config->isArray('foo'));
        $this->assertTrue($config->isArray('bar'));
        $this->assertTrue($config->isArray('bar.baz'));
        $this->assertTrue($config->isArray('bar.baz.oof'));
        $this->assertFalse($config->isArray('bar.baz.oof', false));
        $this->assertFalse($config->isArray('isEnabled'));
    }
}
