<?php

// require
require '../../../../../../init.php';

// define the application
define('APPLICATION', 'backend');

// initialize components
new BackendInit(APPLICATION);


/**
 * This function must check the user session to be sure that he/she is
 * authorized to upload and access files in the File Browser.
 *
 * @return boolean
 */
function CheckAuthentication()
{
	return BackendAuthentication::isLoggedIn();
}

// LicenseKey : Paste your license key here. If left blank, CKFinder will be fully functional, in demo mode.
$config['LicenseName'] = BackendModel::getModuleSetting('core', 'ckfinder_license_name');
$config['LicenseKey'] = BackendModel::getModuleSetting('core', 'ckfinder_license_key');

/**
 * The base path used to build the final URL for the resources handled
 * @remark the trailing slash is required.
 *
 * @var	string
 */
$baseUrl = '/frontend/files/userfiles/';

/**
 * The path to the local directory (in the server) which points to the above $baseUrl URL. This is the path used by CKFinder to handle the files in the server. Full write permissions must be granted to this directory.
 * @remark the trailing slash is required.
 *
 * @var	string
 */
$baseDir = FRONTEND_FILES_PATH . '/userfiles/';

// thumbnails : thumbnails settings. All thumbnails will end up in the same directory, no matter the resource type.
$config['Thumbnails'] = array('url' => $baseUrl . '_thumbs', 'directory' => $baseDir . '_thumbs', 'enabled' => true, 'directAccess' => true, 'maxWidth' => 96, 'maxHeight' => 96, 'bmpSupported' => false, 'quality' => 100);

// set the maximum size of uploaded images. If an uploaded image is larger, it gets scaled down proportionally. Set to 0 to disable this feature.
$config['Images'] = array('maxWidth' => BackendModel::getModuleSetting('core', 'ckfinder_image_max_width'), 'maxHeight' => BackendModel::getModuleSetting('core', 'ckfinder_image_max_height'), 'quality' => 100);

// the session variable name that CKFinder must use to retrieve the "role" of the current user. The "role", can be used in the "AccessControl" settings (bellow in this page).
$config['RoleSessionVar'] = 'CKFinder_UserRole';

/**
 * AccessControl : used to restrict access or features to specific folders.
 * Many "AccessControl" entries can be added. All attributes are optional. Subfolders inherit their default settings from their parents' definitions.
 * - The "role" attribute accepts the special '*' value, which means "everybody".
 * - The "resourceType" attribute accepts the special value '*', which means "all resource types".
 */
$config['AccessControl'][] = array('role' => '*', 'resourceType' => '*', 'folder' => '/', 'folderView' => true, 'folderCreate' => true, 'folderRename' => true, 'folderDelete' => true, 'fileView' => true, 'fileUpload' => true, 'fileRename' => true, 'fileDelete' => true);

/**
 * ResourceType : defines the "resource types" handled in CKFinder. A resource type is nothing more than a way to group files under different paths, each one having different configuration settings.
 * Each resource type name must be unique.
 *
 * When loading CKFinder, the "type" querystring parameter can be used to display a specific type only. If "type" is omitted in the URL, the "DefaultResourceTypes" settings is used (may contain the resource type names separated by a comma). If left empty, all types are loaded.
 * maxSize is defined in bytes, but shorthand notation may be also used. Available options are: G, M, K (case insensitive).
 * 1M equals 1048576 bytes (one Megabyte), 1K equals 1024 bytes (one Kilobyte), 1G equals one Gigabyte.
 * */
$config['DefaultResourceTypes'] = '';
$config['ResourceType'][] = array(
	'name' => 'Files',
	'url' => $baseUrl . 'files',
	'directory' => $baseDir . 'files',
	'maxSize' => 0,
	'allowedExtensions' => '7z,aiff,asf,avi,bmp,csv,doc,fla,flv,gif,gz,gzip,jpeg,jpg,mid,mov,mp3,mp4,mpc,mpeg,mpg,ods,odt,pdf,png,ppt,pxd,qt,ram,rar,rm,rmi,rmvb,rtf,sdc,sitd,swf,sxc,sxw,tar,tgz,tif,tiff,txt,vsd,wav,wma,wmv,xls,xml,zip',
	'deniedExtensions' => ''
);
$config['ResourceType'][] = array(
	'name' => 'Images',
	'url' => $baseUrl . 'images',
	'directory' => $baseDir . 'images',
	'maxSize' => 0,
	'allowedExtensions' => 'gif,jpeg,jpg,png',
	'deniedExtensions' => ''
);

$config['CheckDoubleExtension'] = true;
$config['FilesystemEncoding'] = 'UTF-8';
$config['SecureImageUploads'] = true;
$config['CheckSizeAfterScaling'] = true;

// for security, HTML is allowed in the first Kb of data for files having the following extensions only.
$config['HtmlExtensions'] = array('html', 'htm', 'xml', 'js');
// folders to not display in CKFinder, no matter their location. No paths are accepted, only the folder name. The * and ? wildcards are accepted.
$config['HideFolders'] = array('.svn', 'CVS', '.git');
// files to not display in CKFinder, no matter their location. No paths are accepted, only the file name, including extension. The * and ? wildcards are accepted.
$config['HideFiles'] = Array(".*");
// after file is uploaded, sometimes it is required to change its permissions so that it was possible to access it at the later time. If possible, it is recommended to set more restrictive permissions, like 0755. Set to 0 to disable this feature.
$config['ChmodFiles'] = 0777 ;
$config['ChmodFolders'] = 0755 ;
// force ASCII names for files and folders. If enabled, characters with diactric marks, like å, ä, ö, ć, č, đ, š will be automatically converted to ASCII letters.
$config['ForceAscii'] = true;

include_once 'plugins/imageresize/plugin.php';
include_once 'plugins/fileeditor/plugin.php';

$config['plugin_imageresize']['smallThumb'] = '90x90';
$config['plugin_imageresize']['mediumThumb'] = '120x120';
$config['plugin_imageresize']['largeThumb'] = '180x180';
