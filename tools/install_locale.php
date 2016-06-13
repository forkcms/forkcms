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
$shortOptions = 'f:m:o::';
$options = getopt($shortOptions);

// No file was given
if (!isset($options['f']) && !isset($options['m'])) {
    throw new Exception('We need a file or module to load our data from.');
}

// File given, check if it exists
elseif (isset($options['f'])) {
    $baseFile = $options['f'];

    if (!file_exists($baseFile)) {
        throw new Exception('The given file(' . $baseFile . ') does not exist.');
    }
}

// Module name given, check if it exists
else {
    $baseFile = __DIR__ . '/..' . '/src/Backend/Modules/' . ucfirst($options['m']) . '/Installer/Data/locale.xml';

    if (!file_exists($baseFile)) {
        throw new Exception('The given file(' . $baseFile . ') does not exist.');
    }
}

// bootstrap Fork
define('APPLICATION', 'Backend');
$kernel = new AppKernel('prod', false);

if (!defined('PATH_WWW')) {
    define('PATH_WWW', __DIR__ . '/..');
}
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
if ($xmlData === false) {
    throw new Exception('Invalid locale.xml file.');
}

// Everything ok, let's install the locale
$results = BackendLocaleModel::importXML($xmlData, $overwrite, null, null, 1);

if (!$results['total'] > 0) {
    throw new Exception('Something went wrong during import.');
} else {
    if ($results['imported'] > 0) {
        echo 'Locale installed successfully' . "\n";

        return;
    }

    if ($results['imported'] == 0) {
        echo 'No locale was installed. Try adding the overwrite (-o) option.' . "\n";

        return;
    }
}
