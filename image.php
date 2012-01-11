<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

// set the library path
require_once 'frontend/cache/config/config.php';
set_include_path(INIT_PATH_LIBRARY);

// get the basics
require_once 'globals.php';
require_once 'spoon/spoon.php';

/**
 * This class will get the needed parameters and see if the image exists, if so it will be resized to
 * the wanted format if this hasn't been done before.
 *
 * @author Jelmer Snoeck <jelmer.snoeck@netlash.com>
 */
class ImageResizer
{
	/**
	 * @var string
	 */
	protected $action, $file, $filePath, $module, $sourcePath, $type;

	/**
	 * @var int
	 */
	protected $height, $width;

	/**
	 * @var bool
	 */
	protected $crop = true, $allowEnlargement;

	public function __construct()
	{
		if($this->getParameters())
		{
			$this->buildFilePath();
		}
	}

	/**
	 * Build the file path according to the type or the width and height of an image
	 */
	public function buildFilePath()
	{
		$this->filePath = PATH_WWW . '/frontend/files/' . $this->module . '/' . $this->action . '/';
		$this->sourcePath = $this->filePath . 'source';

		if($this->height != '' && $this->width != '') $this->filePath .= $this->width . 'x' . $this->height;
		else $this->filePath .= 'source';
	}

	/**
	 * This will fetch the parameters needed to load the image
	 *
	 * @return bool
	 */
	public function getParameters()
	{
		// optional parameters
		$this->module = SpoonFilter::getGetValue('module', null, 'userfiles');
		$this->action = SpoonFilter::getGetValue('action', null, 'images');
		$this->crop = SpoonFilter::getGetValue('crop', array('true', 'false'), true);
		$this->allowEnlargement = SpoonFilter::getGetValue('enlargement', array('true', 'false'), false);

		// these are required if the type is not the source
		$this->height = SpoonFilter::getGetValue('height', null, 0, 'int');
		$this->width = SpoonFilter::getGetValue('width', null, 0, 'int');
		if($this->height <= 0 || $this->width <= 0)
		{
			if(SPOON_DEBUG) throw new Exception('Not all parameters are given');
			else return false;
		}

		$this->file = SpoonFilter::getGetValue('file', null, null);
		if($this->file == '')
		{
			if(SPOON_DEBUG) throw new Exception('No image given');
			else return false;
		}

		return true;
	}

	/**
	 * This will output the image
	 */
	public function output()
	{
		if(SpoonFile::exists($this->filePath . '/' . $this->file))
		{
			// output this file
			readfile($this->filePath . '/' . $this->file);
		}
		// we need to create the image from the source
		elseif(SpoonFile::exists($this->sourcePath . '/' . $this->file) && $this->height != '' && $this->width != '')
		{
			// validate the sizes
			$this->validateSizes();

			// create a new thumbnail instance
			$thumbnail = new SpoonThumbnail($this->sourcePath . '/' . $this->file, $this->width, $this->height);

			// set the options
			if($this->crop) $thumbnail->setForceOriginalAspectRatio(false);
			if($this->allowEnlargement) $thumbnail->setAllowEnlargement(true);

			// parse the file
			$thumbnail->parseToFile($this->filePath . '/' . $this->file);

			// re output
			$this->output();
		}
	}

	/**
	 * Validate the image sizes from the source file against the given sizes.
	 */
	public function validateSizes()
	{
		list($width, $height) = getimagesize($this->sourcePath . '/' . $this->file);

		if($width < $this->width) $this->width = (int) $width;
		if($height < $this->height) $this->height = (int) $height;
	}
}

// do the image magic!
$image = new ImageResizer();
$image->output();
