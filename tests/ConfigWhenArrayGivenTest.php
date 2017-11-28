<?php

namespace Fulcrum\Config\Tests;

use Fulcrum\Config\Config;

class ConfigWhenArrayGivenTest extends \PHPUnit_Framework_TestCase
{
    protected static $testArray = [
        'foo' => [
            'platform' => 'WordPress',
            'theme'    => 'Beans',
        ],
        'bar' => [
            'baz' => [
                'who'        => 'Tonya',
                'someNumber' => 300,
            ],
        ],
    ];

    protected static $defaultsArray = [
        'foo'       => [
            'platform' => '',
            'theme'    => '',
            'site'     => 'wpfulcrum',
        ],
        'bar'       => [
            'baz' => [
                'who' => '',
                'oof' => [],
            ],
        ],
        'isEnabled' => true,
    ];

    public function testCreate()
    {
        $config = new Config(self::$testArray);
        $this->assertInstanceOf(Config::class, $config);

        $config = new Config(self::$testArray, self::$defaultsArray);
        $this->assertInstanceOf(Config::class, $config);
    }

    public function testHasKey()
    {
        $config = new Config(self::$testArray);

        $this->assertTrue($config->has('foo'));
        $this->assertFalse($config->has('oof'));
        $this->assertTrue($config->has('foo.platform'));
        $this->assertTrue($config->has('bar'));
        $this->assertFalse($config->has('baz'));
        $this->assertTrue($config->has('bar.baz.who'));
        $this->assertFalse($config->has('bar.baz.who.foobar'));
        $this->assertTrue($config->has('bar.baz.someNumber'));

        // These are loaded from the defaults.
        $this->assertFalse($config->has('isEnabled'));
        $this->assertFalse($config->has('bar.baz.oof'));
        $this->assertFalse($config->has('foo.site'));
    }

    public function testHasKeyWhenDefaultsGiven()
    {
        $config = new Config(self::$testArray, self::$defaultsArray);

        $this->assertTrue($config->has('foo'));
        $this->assertFalse($config->has('oof'));
        $this->assertTrue($config->has('foo.platform'));
        $this->assertTrue($config->has('bar'));
        $this->assertFalse($config->has('baz'));
        $this->assertTrue($config->has('bar.baz.who'));
        $this->assertFalse($config->has('bar.baz.who.foobar'));
        $this->assertTrue($config->has('bar.baz.someNumber'));

        // These are loaded from the defaults.
        $this->assertTrue($config->has('isEnabled'));
        $this->assertTrue($config->has('bar.baz.oof'));
        $this->assertTrue($config->has('foo.site'));
    }

    public function testGet()
    {
        $config = new Config(self::$testArray);
        $this->assertEquals('WordPress', $config->get('foo.platform'));
        $this->assertEquals('Beans', $config->get('foo.theme'));
        $this->assertNull($config->get('foo.site'));
        $this->assertEquals('Tonya', $config->get('bar.baz.who'));
        $this->assertEquals(300, $config->get('bar.baz.someNumber'));

        $config = new Config(self::$testArray, self::$defaultsArray);
        $this->assertTrue($config->get('isEnabled'));
        $this->assertEquals([], $config->get('bar.baz.oof'));
        $this->assertEquals('wpfulcrum', $config->get('foo.site'));
    }

    public function testGetDefault()
    {
        $config = new Config(self::$testArray);

        $this->assertNull($config->get('doesnotexist'));
        $this->assertFalse($config->get('doesnotexist', false));
        $this->assertEquals(10, $config->get('doesnotexist', 10));
        $this->assertEquals([], $config->get('doesnotexist', []));
    }

    public function testIsArray()
    {
        $config = new Config(self::$testArray, self::$defaultsArray);

        $this->assertTrue($config->isArray('foo'));
        $this->assertTrue($config->isArray('bar'));
        $this->assertTrue($config->isArray('bar.baz'));
        $this->assertTrue($config->isArray('bar.baz.oof'));
        $this->assertFalse($config->isArray('bar.baz.oof', false));
        $this->assertFalse($config->isArray('isEnabled'));
    }

    public function testSet()
    {
        $config = new Config(self::$testArray);

        $config->who = 'John';
        $this->assertEquals( 'John', $config->who );

        $config->foo = 'Software development is fun!';
        $this->assertEquals( 'Software development is fun!', $config->foo );

        $config->offsetSet( 'bar', 'Beans rocks!' );
        $this->assertEquals( 'Beans rocks!', $config->bar );

        $config->push( 'foobar', 'Lemon water is awesome' );
        $this->assertEquals( 'Lemon water is awesome', $config->foobar );

        $config->set( 'baz', 'Know the fundamentals first' );
        $this->assertEquals( 'Know the fundamentals first', $config->baz );
    }

    public function testHasAfterSet() {
        $config = new Config(self::$testArray, self::$defaultsArray);

        $config->isEnabled = false;
        $this->assertTrue( $config->has( 'isEnabled' ) );
        $this->assertFalse( $config->isEnabled );

        $config->foobar = 'Software development is fun!';
        $this->assertTrue( $config->has( 'foobar' ) );
        $this->assertEquals( 'Software development is fun!', $config->foobar );

        $config->offsetSet( 'bar.baz.newProperty', 'Beans rocks!' );
        $this->assertTrue( $config->has( 'bar.baz.newProperty' ) );
        $this->assertEquals( 'Beans rocks!', $config->bar['baz']['newProperty'] );


        $config->push( 'foobaz', 'Lemon water is awesome' );
        $this->assertTrue( $config->has( 'foobaz' ) );
        $this->assertEquals( 'Lemon water is awesome', $config->foobaz );

        $config->set( 'foo.description', 'Know the fundamentals first' );
        $this->assertTrue( $config->has( 'foo.description' ) );
        $this->assertEquals( 'Know the fundamentals first', $config->foo['description'] );
    }

    public function testMerge()
    {
        $config = new Config(self::$testArray);

        $config->merge([
            'foo' => [
                'platform' => ['JavaScript', 'PHP', 'CSS', 'SQL'],
                'metadata' => 'foobar',
                'numPosts' => 10,
            ],
            'barbaz' => [
                'foo' => 1,
            ],
            'isLoading' => false,
        ]);

        $this->assertTrue($config->has('foo.metadata'));
        $this->assertEquals('foobar', $config->get('foo.metadata'));
        $this->assertTrue($config->has('isLoading'));
        $this->assertFalse($config->get('isLoading'));
        $this->assertFalse($config->has('foobar'));
        $this->assertTrue($config->has('barbaz.foo'));
        $this->assertEquals([
            'foo' => 1,
        ], $config->get('barbaz'));
        $this->assertSame(['JavaScript', 'PHP', 'CSS', 'SQL'], $config['foo.platform']);
    }
}
