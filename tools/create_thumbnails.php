<?php
/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * USAGE WITH DELTA SCRIPT
 * -----------------------
 *
 * Arguments:
 * 		1: module name
 * 		2: map name (images, pictures, ...)
 * 		3: array with new sizes
 *
 * require_once __DIR__ . '/../../tools/create_thumbnails.php';
 * createThumbnails('products', 'pictures', array('200x500'));
 *
 *
 * USAGE THROUGH COMMAND LINE
 * --------------------------
 *
 * Arguments:
 * 		1: module name
 * 		2: map name (images, pictures, ...)
 *
 * php create_thumbnails.php products pictures
 */

/**
 * This script can be used to recreate thumbnailes, add new sizes, etc...
 *
 * @author Jonas Goderis <jonas.goderis@wijs.be>
 */

// some requirements
require_once dirname(__FILE__) . '/../backend/init.php';

// init new backend cronjob
new BackendInit('backend_cronjob');
define('NAMED_APPLICATION', 'backend_cronjob');

// set language
BackendLanguage::setWorkingLanguage('nl');

// set some vars when we run this script in the command line
if(count($argv) > 1)
{
	$module = isset($argv[1]) ? (string) $argv[1] : null;
	$map = isset($argv[2]) ? (string) $argv[2] : null;

	// create the thumbnails
	createThumbnails($module, $map);
}

/**
 * Create thumbnails
 *
 * @param  string $module
 * @param  string $map
 * @param  array $sizes
 * @return void
 */
function createThumbnails($module, $map, $sizes = array())
{
	// check if we have a module & map
	if($module == null || $map == null)
	{
		print('Arguments are missing [0] > module [1] > map' . "\n");
		return;
	}

	// set path
	$frontendFiles = dirname(__FILE__) . '/../frontend/files';
	$imagesPath = $frontendFiles . '/' . $module . '/' . $map;
	$sourcePath = $imagesPath . '/source';

	// check if the source path exists
	if(!is_dir($sourcePath))
	{
		print('The arguments are not valid and the directory could not be found.' . "\n");
		return;
	}

	// create directories for new sizes if any given
	if(!empty($sizes))
	{
		foreach($sizes as $size)
		{
			$directory = $imagesPath . '/' . $size;
			if(!SpoonDirectory::exists($directory)) SpoonDirectory::create($directory);
		}
	}

	// get all the source images
	$pictures = SpoonFile::getList($sourcePath, '/^.*\.(jpg|jpeg|png|gif)$/i');

	foreach($pictures as $picture)
	{
		BackendModel::generateThumbnails($imagesPath, $sourcePath . '/' . $picture);
		print('generating thumbnails for: ' . $picture . "\n");
	}
}