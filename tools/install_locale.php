<?php

/*
 * This is a simple script to install a locale file.
 *
 * @author Jelmer Snoeck <jelmer.snoeck@wijs.be>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@wijs.be>
 */

require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/../app/AppKernel.php';
require_once __DIR__ . '/../app/KernelLoader.php';

use Backend\Init as BackendInit;
use Backend\Modules\Locale\Engine\Model as BackendLocaleModel;

/*
 * The short options explained:
 * f: => file (argument required)
 * o => overwrite the locale or not? (no argument required)
 */
$shortOptions = 'f:o';
$options = getopt($shortOptions);

// No file was given
if(!isset($options['f']))
{
	throw new Exception('We need a file to load our data from.');
}

// File given, check if it exists
else
{
	$baseFile = $options['f'];

	if(!file_exists($baseFile))
	{
		throw new Exception('The given file(' . $baseFile . ') does not exist.');
	}
}

// bootstrap Fork
DEFINE('APPLICATION', 'Backend');
if (!defined('PATH_WWW')) {
    define('PATH_WWW', '__DIR__' . '/..');
}
$kernel = new AppKernel('prod', false);
$kernel->boot();
$kernel->defineForkConstants();
$loader = new BackendInit($kernel);
$loader->initialize('Backend');
$loader->passContainerToModels();

// create needed constants
$container = $kernel->getContainer();

// Set the overwrite parameter
$overwrite = (isset($options['o']));

// Set the basedir
$baseDir = getcwd() . '/..';

// load the xml from the file
$xmlData = @simplexml_load_file($baseFile);

// this is an invalid xml file
if($xmlData === false)
{
	throw new Exception('Invalid locale.xml file.');
}

// Everything ok, let's install the locale
BackendLocaleModel::importXML($xmlData, $overwrite, null, null, 1);
