<?php
/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 *
 * This script can be used to recreate thumbnailes, add new sizes, etc...
 *
 * @author Jonas Goderis <jonas.goderis@wijs.be>
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

require __DIR__ . '/../autoload.php';
require __DIR__ . '/../app/AppKernel.php';
require_once __DIR__ . '/../app/KernelLoader.php';

use Backend\Init as BackendInit;
use Backend\Core\Engine\Model as BackendModel;

DEFINE('APPLICATION', 'Backend');
$kernel = new AppKernel('prod', false);
$kernel->boot();
$kernel->defineForkConstants();
$loader = new BackendInit($kernel);
$loader->initialize('Backend');
$loader->passContainerToModels();

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
	$frontendFiles = dirname(__FILE__) . '/../src/Frontend/Files';
	$imagesPath = $frontendFiles . '/' . $module . '/' . $map;
	$sourcePath = $imagesPath . '/source';

	// check if the source path exists
	if(!SpoonDirectory::exists($sourcePath))
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
