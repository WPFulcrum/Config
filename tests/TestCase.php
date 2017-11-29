<?php

namespace Fulcrum\Config\Tests;

use Brain\Monkey;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    protected $isLoaded = false;
    protected $testArrayPath;
    protected $defaultsPath;
    protected $testArray;
    protected $defaults;

    /**
     * Prepares the test environment before each test.
     */
    protected function setUp()
    {
        parent::setUp();
        Monkey\setUp();

        if (!$this->isLoaded) {
            $this->testArrayPath = FULCRUM_CONFIG_TESTS_DIR . '/fixtures/test-array.php';
            $this->defaultsPath  = FULCRUM_CONFIG_TESTS_DIR . '/fixtures/defaults.php';
            $this->testArray     = require $this->testArrayPath;
            $this->defaults      = require $this->defaultsPath;
            $this->isLoaded      = true;
        }
    }

    /**
     * Cleans up the test environment after each test.
     */
    protected function tearDown()
    {
        Monkey\tearDown();
        parent::tearDown();
    }
}
