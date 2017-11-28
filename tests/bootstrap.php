<?php

namespace Fulcrum\Config\Tests;

if ( version_compare( phpversion(), '5.6.0', '<' ) ) {
    die( 'Whoops, PHP 5.6 or higher is required.' );
}

define( 'FULCRUM_CONFIG_TESTS_DIR', __DIR__ );
define( 'FULCRUM_CONFIG_ROOT_DIR', dirname( __DIR__ ) . DIRECTORY_SEPARATOR );

/**
 * Time to load Composer's autoloader.
 */
$fulcrumConfigAutoloadPath = FULCRUM_CONFIG_ROOT_DIR . 'vendor/';
if ( ! file_exists( $fulcrumConfigAutoloadPath . 'autoload.php' ) ) {
    die( 'Whoops, we need Composer before we start running tests.  Please type: `composer install`.' );
}
require_once $fulcrumConfigAutoloadPath . 'autoload.php';
unset( $fulcrumConfigAutoloadPath );
