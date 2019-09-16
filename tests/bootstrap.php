<?php
/**
 * Bootstrap our application for testing
 *
 * @package SellBooksDirect_UnitTest
 * @subpackage Bootstrap
 * @version $Id: 70fe700ffcbba2629e923e936683f2d5d2c0d8d5 $
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'testing'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();

/** Zend_Application */
require_once 'Zend/Application.php';
require_once 'Zend/Config/Ini.php';

$defaultConfig = new Zend_Config_Ini(
        APPLICATION_PATH . '/configs/application.ini',
        APPLICATION_ENV,
        array('allowModifications' => true)
);

if (file_exists(APPLICATION_PATH . '/configs/local.ini')) {
    $supplementalConfig = new Zend_Config_Ini(
            APPLICATION_PATH . '/configs/local.ini');

    $env = APPLICATION_ENV;
    if (isset($supplementalConfig->$env)) {
        $supplementalConfig = $supplementalConfig->$env;
        $defaultConfig->merge($supplementalConfig);
    }
}

$application = new Zend_Application(
        APPLICATION_ENV,
        $defaultConfig
);
