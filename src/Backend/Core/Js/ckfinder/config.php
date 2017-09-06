<?php

use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Model as BackendModel;
use ForkCMS\App\AppKernel;
use ForkCMS\App\KernelLoader;
use Symfony\Component\HttpFoundation\Request;

$env = getenv('FORK_ENV') ? : 'prod';
$debug = getenv('FORK_DEBUG') === '1';

$kernel = new AppKernel($env, $debug);
$loader = new KernelLoader($kernel);
$loader->passContainerToModels();


/*
 * To make it easy to configure CKFinder, the $baseUrl and $baseDir can be used.
 * Those are helper variables used later in this config file.
 */

/*
 * $baseUrl : the base path used to build the final URL for the resources handled
 * in CKFinder. If empty, the default value (/userfiles/) is used.
 * Examples:
 *   $baseUrl = 'http://example.com/ckfinder/files/';
 *   $baseUrl = '/userfiles/';
 * ATTENTION: The trailing slash is required.
 */
$baseUrl = '/src/Frontend/Files/Core/CKFinder/';

/*
 * $baseDir : the path to the local directory (in the server) which points to the
 * above $baseUrl URL. This is the path used by CKFinder to handle the files in
 * the server. Full write permissions must be granted to this directory.
 *
 * Examples:
 *   // You may point it to a directory directly:
 *   $baseDir = '/home/login/public_html/ckfinder/files/';
 *   $baseDir = 'C:/SiteDir/CKFinder/userfiles/';
 *
 *   // Or you may let CKFinder discover the path, based on $baseUrl.
 *   // WARNING: resolveUrl() *will not work* if $baseUrl does not start with a slash ("/"),
 *   // for example if $baseDir is set to  http://example.com/ckfinder/files/
 *   $baseDir = resolveUrl($baseUrl);
 *
 * ATTENTION: The trailing slash is required.
 */
$baseDir = $kernel->getContainer()->getParameter('site.path_www') . $baseUrl;

/*
 * CKFinder Configuration File
 *
 * For the official documentation visit http://docs.cksource.com/ckfinder3-php/
 */

/*============================ PHP Error Reporting ====================================*/
// http://docs.cksource.com/ckfinder3-php/debugging.html

/*
 * Create a Kernel and load the DI container to be able to access the Backend Model methods and
 * the configuration. This should be refactored in time.
 */

if ($env == 'prod' || $debug == false) {
    // Production
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
    ini_set('display_errors', 0);
} else {
    // Development
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

/*============================ General Settings =======================================*/
// http://docs.cksource.com/ckfinder3-php/configuration.html

$config = array();

/*============================ Enable PHP Connector HERE ==============================*/
// http://docs.cksource.com/ckfinder3-php/configuration.html#configuration_options_authentication

$config['authentication'] = function () {
    $env = getenv('FORK_ENV') ? : 'prod';
    $debug = getenv('FORK_DEBUG') === '1';

    $kernel = new AppKernel($env, $debug);
    $loader = new KernelLoader($kernel);

    BackendModel::get('request_stack')->push(Request::create('/private'));

    return BackendAuthentication::isLoggedIn();
};

/*============================ License Key ============================================*/
// http://docs.cksource.com/ckfinder3-php/configuration.html#configuration_options_licenseKey

$config['licenseName'] = BackendModel::get('fork.settings')->get('Core', 'ckfinder_license_name');
$config['licenseKey']  = BackendModel::get('fork.settings')->get('Core', 'ckfinder_license_key');

/*============================ CKFinder Internal Directory ============================*/
// http://docs.cksource.com/ckfinder3-php/configuration.html#configuration_options_privateDir

$config['privateDir'] = array(
    'backend' => 'default',
    'tags'   => '.ckfinder/tags',
    'logs'   => '.ckfinder/logs',
    'cache'  => '.ckfinder/cache',
    'thumbs' => '.ckfinder/cache/thumbs',
);

/*============================ Images and Thumbnails ==================================*/
// http://docs.cksource.com/ckfinder3-php/configuration.html#configuration_options_images

$config['images'] = array(
    'maxWidth'  => BackendModel::get('fork.settings')->get('Core', 'ckfinder_image_max_width'),
    'maxHeight' => BackendModel::get('fork.settings')->get('Core', 'ckfinder_image_max_height'),
    'quality'   => 100,
    'sizes' => array(
        'small'  => array('width' => 480, 'height' => 320, 'quality' => 80),
        'medium' => array('width' => 600, 'height' => 480, 'quality' => 80),
        'large'  => array('width' => 800, 'height' => 600, 'quality' => 80)
    )
);

/*=================================== Backends ========================================*/
// http://docs.cksource.com/ckfinder3-php/configuration.html#configuration_options_backends

$config['backends'][] = array(
    'name'         => 'default',
    'adapter'      => 'local',
    'baseUrl'      => $baseUrl,
    'root'         => $baseDir, // Can be used to explicitly set the CKFinder user files directory.
    'chmodFiles'   => 0777,
    'chmodFolders' => 0755,
    'filesystemEncoding' => 'UTF-8',
);

/*================================ Resource Types =====================================*/
// http://docs.cksource.com/ckfinder3-php/configuration.html#configuration_options_resourceTypes

$config['defaultResourceTypes'] = '';

$config['resourceTypes'][] = array(
    'name'              => 'Files', // Single quotes not allowed.
    'directory'         => 'files',
    'maxSize'           => 0,
    'allowedExtensions' => '7z,aiff,asf,avi,bmp,csv,doc,docx,eps,fla,flv,gif,gz,gzip,jpeg,jpg,mid,mov,mp3,mp4,mpc,mpeg,mpg,ods,odt,pdf,png,ppt,pptx,pxd,qt,ram,rar,rm,rmi,rmvb,rtf,sdc,sitd,svg,swf,sxc,sxw,tar,tgz,tif,tiff,txt,vsd,wav,webp,wma,wmv,xls,xlsx,zip',
    'deniedExtensions'  => '',
    'backend'           => 'default'
);

$config['resourceTypes'][] = array(
    'name'              => 'Images',
    'directory'         => 'images',
    'maxSize'           => 0,
    'allowedExtensions' => 'gif,jpeg,jpg,png,svg,webp',
    'deniedExtensions'  => '',
    'backend'           => 'default'
);

/*================================ Access Control =====================================*/
// http://docs.cksource.com/ckfinder3-php/configuration.html#configuration_options_roleSessionVar

$config['roleSessionVar'] = 'CKFinder_UserRole';

// http://docs.cksource.com/ckfinder3-php/configuration.html#configuration_options_accessControl
$config['accessControl'][] = array(
    'role'                => '*',
    'resourceType'        => '*',
    'folder'              => '/',

    'FOLDER_VIEW'         => true,
    'FOLDER_CREATE'       => true,
    'FOLDER_RENAME'       => true,
    'FOLDER_DELETE'       => true,

    'FILE_VIEW'           => true,
    'FILE_CREATE'         => true,
    'FILE_RENAME'         => true,
    'FILE_DELETE'         => true,

    'IMAGE_RESIZE'        => true,
    'IMAGE_RESIZE_CUSTOM' => true
);


/*================================ Other Settings =====================================*/
// http://docs.cksource.com/ckfinder3-php/configuration.html

$config['overwriteOnUpload'] = false;
$config['checkDoubleExtension'] = true;
$config['disallowUnsafeCharacters'] = false;
$config['secureImageUploads'] = true;
$config['checkSizeAfterScaling'] = true;
$config['htmlExtensions'] = array('html', 'htm', 'xml', 'js');
$config['hideFolders'] = array('.*', 'CVS', '__thumbs');
$config['hideFiles'] = array('.*');
$config['forceAscii'] = false;
$config['xSendfile'] = false;

// http://docs.cksource.com/ckfinder3-php/configuration.html#configuration_options_debug
$config['debug'] = false;

/*==================================== Plugins ========================================*/
// http://docs.cksource.com/ckfinder3-php/configuration.html#configuration_options_plugins

$config['pluginsDirectory'] = __DIR__ . '/plugins';
$config['plugins'] = array();

/*================================ Cache settings =====================================*/
// http://docs.cksource.com/ckfinder3-php/configuration.html#configuration_options_cache

$config['cache'] = array(
    'imagePreview' => 24 * 3600,
    'thumbnails'   => 24 * 3600 * 365,
    'proxyCommand' => 0
);

/*============================ Temp Directory settings ================================*/
// http://docs.cksource.com/ckfinder3-php/configuration.html#configuration_options_tempDirectory

$config['tempDirectory'] = sys_get_temp_dir();

/*============================ Session Cause Performance Issues =======================*/
// http://docs.cksource.com/ckfinder3-php/configuration.html#configuration_options_sessionWriteClose

$config['sessionWriteClose'] = true;

/*================================= CSRF protection ===================================*/
// http://docs.cksource.com/ckfinder3-php/configuration.html#configuration_options_csrfProtection

$config['csrfProtection'] = true;

/*============================== End of Configuration =================================*/

// Config must be returned - do not change it.
return $config;
