# Config Module

The Fulcrum Config Module provides a runtime configuration component for your WordPress project. It's minimal and lean.

Using dependency injection via the `ConfigContract`, a PHP interface, you are able to inject a specific implementation's configuration for each object.  Forget about hard-coding parameters, as these require you to change them for each implementation or project.  Instead, abstract them into a configuration array and then load that file into `Config`, making your code more readable, reusable, testable, and maintainable.

## Features

This module provides a clean, reusable method of:

1. Abstracting all of your specific implementation's configuration parameters, organizing them in one file.
2. Converting the implementation's array into a `Config` object.
3. When you want a common set of defaults (such as for shortcodes, post types, taxonomies, widgets, and more), pass in the defaults. Bam, the module handles deeply merging those defaults with each of the implementations.
4. You can get one or more of the parameters when you need them using:
    - standard object notation, such as `$this->config->numOfPosts`
    - using the `get()` method with single or "dot" notation.
4. Pushing additional parameters into a single configuration.
5. and more.

## Installation

The best way to use this component is through Composer:

```
composer require wpfulcrum/config
```

## "Dot" Notation

Like all of the Fulcrum modules, we borrow from Laravel's "dot" notation to access deeply nested array elements.  What is "dot" notation? Great question.

Dot notation is a clever mechanism to access deeply nested arrays using a string of the keys separated by dots.  

Here let me show you.  Let's say you have a deeply nested array like this one:

```
$config = new Config(array(
	'autoload'  => true,
	'classname' => 'YourBrand\YourProject\Shortcode\QA',
	'config'    => array(
		'shortcode' => 'qa',
		'view'      => YOURPLUGIN_PATH . 'src/Shortcode/views/qa.php',
		'defaults'  => array(
			'id'         => '',
			'class'      => '',
			'question'   => '',
			'type'       => '',
			'color'      => '',
			'open_icon'  => 'fa fa-chevron-down',
			'close_icon' => 'fa fa-chevron-up',
		),
	),
));
```

To get at shortcode's default open icon, you would do `$config->get('default.open_icon)`.  Notice you using "dot" notation you are able to drill down into the array and select the open icon's value.  

How? It uses the [Fulcrum Extender's DotArray module](https://github.com/wpfulcrum/extender).  Seriously, the Array Module is an awesome PHP extender, making your life much easier when working with deeply nested array.

## Common Basic Usage and Functionality

### Creating a Configuration File

Typically, you will create a PHP file that is stored in a `config/` folder within our theme or plugin.  In that file, you'll build and then return an array of all the specific implementation's configuration parameters.

Let's use the configuration example from above, which is for a QA shortcode:

```
<?php

return [
	'autoload'  => true,
	'classname' => 'YourBrand\YourProject\Shortcode\QA',
	'config'    => [
		'shortcode' => 'qa',
		'view'      => YOURPLUGIN_PATH . 'src/Shortcode/views/qa.php',
		'defaults'  => [
			'id'         => '',
			'class'      => '',
			'question'   => '',
			'type'       => '',
			'color'      => '',
			'open_icon'  => 'fa fa-chevron-down',
			'close_icon' => 'fa fa-chevron-up',
		],
	],
];

```

### Creating a Configurable Object

Using the above configuration file, here's how you might inject it into a Shortcode class:

```
<?php

namespace YourBrand\YourProject\Shortcode;

use Fulcrum\Config\ConfigContract;
use Fulcrum\Custom\Shortcode\Shortcode;

class QA extends Shortcode
{
    /**
     * Runtime configuration parameters.
     * 
     * @var ConfigContract
     */
    protected $config;

    /**
     * QA constructor.
     *
     * @param ConfigContract $config Instance of the runtime configuration parameters for this QA shortcode.
     */
    public function __construct(ConfigContract $config)
    {
        $this->config = $config;
    }
    
    /**
     * Build the Shortcode HTML and then return it.
     *
     * @since 1.0.0
     *
     * @return string Shortcode HTML
     */
    protected function render() {
        $content = do_shortcode( $this->content );

        ob_start();
        include $this->config->view;

        return ob_get_clean();
    }
    
    // left of the code left out for brevity.
}

```

Notice how we define our dependency injection via the `ConfigContract` interface, thereby allowing you to swap out the implementation to a different `Config` repository.

Also notice how we loaded the view file in the `render()` method: `include $this->config->view;`  The parameters you passed in are available to you as an object property or via using the `$this->config->get('view')` method.

### Creating the Config and Injecting It

To create an instance of your configuration parameters, it's best to use the `ConfigFactory`.  You can pass it:

1. The configuration parameters via the path to the configuration file or an array.
2. The default parameters via the path to the default's file or an array.

Here, let me show you some examples using our QA shortcode code above.

#### Example - Via the path to the configuration file

```
$qaShortcode = new QA(
    ConfigFactory::create(YOURPLUGIN_PATH . '/config/shortcode/qa.php')  
);
```

#### Example - Including common defaults

```
$qaShortcode = new QA(
    ConfigFactory::create(
        YOURPLUGIN_PATH . '/config/shortcode/qa.php',
        YOURPLUGIN_PATH . '/config/shortcode/defaults.php
    )  
);
```

## Contributing

All feedback, bug reports, and pull requests are welcome.

## Credits

The "dot" notation and much of the basic structure of the `Config` class is a customized implementation of the [Illuminate Config](https://github.com/illuminate/config) component from Laravel.
