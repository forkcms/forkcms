<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.be
 *
 * @package			spoon
 * @subpackage		image
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @author			Tijs Verkoyen <tijs@spoon-library.be>
 * @since			1.0.0
 */


/** Spoon class */
require_once 'spoon/spoon.php';

/** SpoonFilterException class */
require_once 'spoon/image/exception.php';

/** SpoonFile class */
require_once 'spoon/filesystem/file.php';


/**
 * This class is used to create thumbnails
 *
 * @package			image
 * @subpackage		thumbnail
 *
 *
 * @author			Tijs Verkoyen <tijs@spoon-library.be>
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @since			1.0.0
 */
class SpoonThumbnail
{

	/**
	 * Is enlargement allowed
	 *
	 * @var	bool
	 */
	private $allowEnlargement = false;


	/**
	 * The horizontal crop position
	 *
	 * @var	string
	 */
	private $cropPositionHorizontal = 'center';


	/**
	 * The vertical crop position
	 *
	 * @var	string
	 */
	private $cropPositionVertical = 'middle';


	/**
	 * The path for the original image
	 *
	 * @var	string
	 */
	private $file;


	/**
	 * Must we respect the original aspect ratio?
	 *
	 * @var	bool
	 */
	private $forceOriginalAspectRatio = true;


	/**
	 * The height for the thumbnail
	 *
	 * @var	int
	 */
	private $height;


	/**
	 * The image resource
	 *
	 * @var	resource
	 */
	private $image;


	/**
	 * The width for the thumbnail
	 *
	 * @var int
	 */
	private $width;


	/**
	 * The strict setting
	 *
	 * @var	bool
	 */
	private $strict = SPOON_STRICT;


	/**
	 * Default constructor
	 *
	 * @return	void
	 * @param	string $path
	 * @param	int[optional] $width
	 * @param	int[optional] $height
	 * @param	bool[optional] $strict
	 */
	public function __construct($path, $width = null, $height = null, $strict = null)
	{
		// check if gd is available
		if(!extension_loaded('gd')) throw new SpoonImageException('GD2 isn\'t loaded. Contact your server-admin to enable it.');

		// redefine vars
		$path = (string) $path;
		if($width != null) $width = (int) $width;
		if($height != null) $height = (int) $height;

		// set strict
		if($strict !== null) $this->setStrict($strict);

		// validate
		if(!SpoonFile::exists($path, $this->strict)) throw new SpoonImageException('The sourcefile ('.$path.') couldn\'t be found.');

		// set properties
		$this->file = $path;
		$this->width = $width;
		$this->height = $height;
	}


	/**
	 * Check if file is supported
	 *
	 * @return	bool
	 * @param	string $file
	 */
	public function checkFileType($file)
	{
		// get watermarkfile properties
		list($width, $height, $type) = @getimagesize($file);

		// creae image from sourcefile
		switch($type)
		{
			// gif
			case IMG_GIF:

			// jpg
			case IMG_JPG:

			// png
			case 3:
			case IMG_PNG:
				return true;
			break;

			default:
				return false;
		}
	}


	/**
	 * Outputs the image as png to the browser
	 * use the optional param for disabling the header, usefull for debugging purposes
	 *
	 * @return	void
	 * @param	bool[optional] $headers
	 */
	public function parse($headers = true)
	{
		// set headers
		if($headers) header("Content-type: image/png");

		// get current dimensions
		$imageProperties = @getimagesize($this->file);

		// validate imageProperties
		if($imageProperties === false) throw new SpoonImageException('The sourcefile ('. $this->file .') could not be found.');

		// set current dimensions
		$currentWidth = (int) $imageProperties[0];
		$currentHeight = (int) $imageProperties[1];
		$currentType = (int) $imageProperties[2];
		$currentMime = (string) $imageProperties['mime'];

		// resize image
		$this->resizeImage($currentWidth, $currentHeight, $currentType, $currentMime);

		// output image and cleanup memory
		@imagepng($this->image);
		@imagedestroy($this->image);
	}


	/**
	 * Saves the image to a file (quality is only used for jpg images)
	 *
	 * @return	mixed
	 * @param	string $path
	 * @param	int[optional] $quality
	 * @param	int[optional] $chmod
	 */
	public function parseToFile($path, $quality = 100, $chmod = 0777)
	{
		// redefine vars
		$path = (string) $path;
		$quality = (int) $quality;

		// get extension
		$extension = SpoonFile::getExtension($path);

		// invalid quality
		if($quality > 100 || $quality < 1)
		{
			// strict?
			if($this->strict) throw new SpoonImageException('The quality should be between 1 - 100');
			return false;
		}

		// invalid extension
		if(SpoonFilter::getValue(strtolower($extension), array('gif', 'jpeg', 'jpg', 'png'), '') == '')
		{
			if($this->strict) throw new SpoonImageException('Only gif, jpeg, jpg or png are allowed types.');
			return false;
		}

		// get current dimensions
		$imageProperties = @getimagesize($this->file);

		// validate imageProperties
		if($imageProperties === false)
		{
			// strict?
			if($this->strict) throw new SpoonImageException('The sourcefile ('. $this->file .') could not be found.');
			return false;
		}

		// set current dimensions
		$currentWidth = (int) $imageProperties[0];
		$currentHeight = (int) $imageProperties[1];
		$currentType = (int) $imageProperties[2];
		$currentMime = (string) $imageProperties['mime'];

		// file is the same?
		if($extension == SpoonFile::getExtension($this->file) && $currentWidth == $this->width && $currentHeight == $this->height) return SpoonFile::copy($this->file, $path, true, $chmod);

		// resize image
		$this->resizeImage($currentWidth, $currentHeight, $currentType, $currentMime);

		// output to file
		switch(strtolower($extension))
		{
			case 'gif':
				$return = @imagegif($this->image, $path);
			break;

			case 'jpeg':
			case 'jpg':
				$return = @imagejpeg($this->image, $path, $quality);
			break;

			case 'png':
				$return = @imagepng($this->image, $path);
			break;
		}

		// chmod
		SpoonFile::chmod($path, $chmod);

		// cleanup memory
		@imagedestroy($this->image);

		// return success
		return (bool) $return;
	}


	/**
	 * This internal function will resize/crop the image
	 *
	 * @return	void
	 * @param	int $currentWidth
	 * @param	int $currentHeight
	 * @param	int $currentType
	 * @param	string $currentMime
	 */
	private function resizeImage($currentWidth, $currentHeight, $currentType, $currentMime)
	{
		// check if needed dimensions are present
		if(!$this->forceOriginalAspectRatio) $this->resizeImageWithoutForceAspectRatio($currentWidth, $currentHeight, $currentType, $currentMime);

		// FAR is on
		else $this->resizeImageWithForceAspectRatio($currentWidth, $currentHeight, $currentType, $currentMime);
	}


	/**
	 * Resize the image with Force Aspect Ratio
	 *
	 * @return 	void
	 * @param	int $currentWidth
	 * @param	int $currentHeight
	 * @param	int $currentType
	 * @param	string $currentMime
	 */
	private function resizeImageWithForceAspectRatio($currentWidth, $currentHeight, $currentType, $currentMime)
	{
		// current width is larger then current height
		if($currentWidth > $currentHeight)
		{
			// width is specified
			if($this->width !== null)
			{
				// width is specified
				$newWidth = $this->width;

				// calculate new height
				$newHeight = (int) floor($currentHeight * ($this->width / $currentWidth));
			}

			// height is specified
			elseif($this->height !== null)
			{
				// height is specified
				$newHeight = $this->height;

				// calculate new width
				$newWidth = (int) floor($currentWidth * ($this->height / $currentHeight));
			}

			// no dimensions
			else throw new SpoonImageException('No width or height specified.');
		}

		// current width equals current height
		if($currentWidth == $currentHeight)
		{
			// width is specified
			if($this->width !== null)
			{
				$newWidth = $this->width;
				$newHeight = $this->width;
			}

			// height is specified
			elseif($this->height !== null)
			{
				$newWidth = $this->height;
				$newHeight = $this->height;
			}

			// no dimensions
			else throw new SpoonImageException('No width or height specified.');
		}

		// current width is smaller then current height
		if($currentWidth < $currentHeight)
		{
			// height is specified
			if($this->height !== null)
			{
				// height is specified
				$newHeight = $this->height;

				// calculate new width
				$newWidth = (int) floor($currentWidth * ($this->height / $currentHeight));
			}

			// width is specified
			elseif($this->width !== null)
			{
				// width is specified
				$newWidth = $this->width;

				// calculate new height
				$newHeight = (int) floor($currentHeight * ($this->width / $currentWidth));
			}

			// no dimensions
			else throw new SpoonImageException('No width or height specified.');
		}

		// read current image
		switch ($currentType)
		{
			case IMG_GIF:
				$currentImage = @imagecreatefromgif($this->file);
			break;

			case IMG_JPG:
				$currentImage = @imagecreatefromjpeg($this->file);
			break;

			case 3:
			case IMG_PNG:
				$currentImage = @imagecreatefrompng($this->file);
			break;

			default:
				throw new SpoonImageException('The file you specified ('. $currentMime .') is not supported. Only gif, jpeg, jpg and png are supported.');
		}

		// create image resource
		$this->image = @imagecreatetruecolor($newWidth, $newHeight);

		// set transparent
		@imagealphablending($this->image, false);

		// transparency supported
		if(in_array($currentType, array(IMG_GIF, 3, IMG_PNG)))
		{
			$colorTransparent = @imagecolorallocatealpha($this->image, 0, 0, 0, 127);
			@imagefill($this->image, 0, 0, $colorTransparent);
			@imagesavealpha($this->image, true);
		}

		// resize
		$success = @imagecopyresampled($this->image, $currentImage, 0, 0, 0, 0, $newWidth, $newHeight, $currentWidth, $currentHeight);

		// image creation fail
		if(!$success)
		{
			if($this->strict) throw new SpoonImageException('Something went wrong while trying to resize the image.');
			return false;
		}

		// reset if needed
		if(!$this->allowEnlargement && $currentWidth <= $newWidth && $currentHeight <= $newHeight) $this->image = $currentImage;

		// set transparency for GIF, or try to
		if($currentType == IMG_GIF)
		{
			// get transparent index
			$transparentIndex = @imagecolortransparent($currentImage);

			// valid index
			if($transparentIndex > 0)
			{
				// magic
				$transparentColor = @imagecolorsforindex($currentImage, $transparentIndex);
				$transparentIndex = @imagecolorallocate($this->image, $transparentColor['red'], $transparentColor['green'], $transparentColor['blue']);

				// fill
				@imagefill($this->image, 0, 0, $transparentIndex);
				@imagecolortransparent($this->image, $transparentIndex);
			}
		}
	}


	/**
	 * Resize the image without Force Aspect Ratio
	 *
	 * @return 	void
	 * @param	int $currentWidth
	 * @param	int $currentHeight
	 * @param	int $currentType
	 * @param	string $currentMime
	 */
	private function resizeImageWithoutForceAspectRatio($currentWidth, $currentHeight, $currentType, $currentMime)
	{
		// validate
		if($this->width === null || $this->height === null) throw new SpoonImageException('If forceAspectRatio is false you have to specify width and height.');

		// set new size
		$newWidth = $this->width;
		$newHeight = $this->height;

		// read current image
		switch ($currentType)
		{
			case IMG_GIF:
				$currentImage = @imagecreatefromgif($this->file);
			break;

			case IMG_JPG:
				$currentImage = @imagecreatefromjpeg($this->file);
			break;

			case 3:
			case IMG_PNG:
				$currentImage = @imagecreatefrompng($this->file);
			break;

			default:
				throw new SpoonImageException('The file you specified ('. $currentMime .') is not supported. Only gif, jpeg, jpg and png are supported.');
		}

		// current width is larger then current height
		if($currentWidth > $currentHeight)
		{
			$tempHeight = $this->height;
			$tempWidth = (int) floor($currentWidth * ($this->height / $currentHeight));
		}

		// current width equals current height
		if($currentWidth == $currentHeight)
		{
			$tempWidth = $this->width;
			$tempHeight = $this->width;
		}

		// current width is smaller then current height
		if($currentWidth < $currentHeight)
		{
			$tempWidth = $this->width;
			$tempHeight = (int) floor($currentHeight * ($this->width / $currentWidth));
		}

		// recalculate
		if($tempWidth < $this->width || $tempHeight < $this->height)
		{
			// current width is smaller than the current height
			if($currentWidth < $currentHeight)
			{
				$tempHeight = $this->height;
				$tempWidth = (int) floor($currentWidth * ($this->height / $currentHeight));
			}

			// current width is greater than the current height
			if($currentWidth > $currentHeight)
			{
				$tempWidth = $this->width;
				$tempHeight = (int) floor($currentHeight * ($this->width / $currentWidth));
			}
		}

		// create image resource
		$tempImage = @imagecreatetruecolor($tempWidth, $tempHeight);

		// set transparent
		@imagealphablending($tempImage, false);
		@imagesavealpha($tempImage, true);

		// resize
		$success = @imagecopyresampled($tempImage, $currentImage, 0, 0, 0, 0, $tempWidth, $tempHeight, $currentWidth, $currentHeight);

		// destroy original image
		imagedestroy($currentImage);

		// image creation fail
		if(!$success)
		{
			if($this->strict) throw new SpoonImageException('Something went wrong while resizing the image.');
			return false;
		}

		// calculate horizontal crop position
		switch($this->cropPositionHorizontal)
		{
			case 'left':
				$x = 0;
			break;

			case 'center':
				$x = (int) floor(($tempWidth - $this->width) / 2);
			break;

			case 'right':
				$x = (int) $tempWidth - $this->width;
			break;
		}

		// calculate vertical crop position
		switch($this->cropPositionVertical)
		{
			case 'top':
				$y = 0;
			break;

			case 'middle':
				$y = (int) floor(($tempHeight - $this->height) / 2);
			break;

			case 'bottom':
				$y = (int) $tempHeight - $this->height;
			break;
		}

		// init vars
		$newWidth = $this->width;
		$newHeight = $this->height;

		// validate
		if(!$this->allowEnlargement && ($newWidth > $currentWidth || $newHeight > $currentHeight))
		{
			if($this->strict) throw new SpoonImageException('The specified width/height is larger then the original width/height. Please enable allowEnlargement.');
			return false;
		}

		// create image resource
		$this->image = @imagecreatetruecolor($this->width, $this->height);

		// set transparent
		@imagealphablending($this->image, false);
		$colorTransparent = @imagecolorallocatealpha($this->image, 0, 0, 0, 127);
		@imagefill($this->image, 0, 0, $colorTransparent);
		@imagesavealpha($this->image, true);

		// resize
		$success = @imagecopyresampled($this->image, $tempImage, 0, 0, $x, $y, $newWidth, $newHeight, $newWidth, $newHeight);

		// destroy temp
		@imagedestroy($tempImage);

		// image creation fail
		if(!$success)
		{
			if($this->strict) throw new SpoonImageException('Something went wrong while resizing the image.');
			return false;
		}

		// set transparent for GIF
		if($currentType == IMG_GIF)
		{
			// get transparent index
			$transparentIndex = @imagecolortransparent($currentImage);

			// valid index
			if($transparentIndex > 0)
			{
				// magic
				$transparentColor = @imagecolorsforindex($currentImage, $transparentIndex);
				$transparentIndex = @imagecolorallocate($this->image, $transparentColor['red'], $transparentColor['green'], $transparentColor['blue']);

				// fill
				@imagefill($this->image, 0, 0, $transparentIndex);
				@imagecolortransparent($this->image, $transparentIndex);
			}
		}
	}


	/**
	 * set the allowEnlargement, default is false
	 *
	 * @return	void
	 * @param	bool[optional] $on
	 */
	public function setAllowEnlargement($on = false)
	{
		$this->allowEnlargement = (bool) $on;
	}


	/**
	 * Sets the horizontal and vertical cropposition
	 *
	 * @return	mixed
	 * @param	string $horizontal	(left, center*, right)
	 * @param	string $vertical	(top, middle*, bottom)
	 */
	public function setCropPosition($horizontal = 'center', $vertical = 'middle')
	{
		// redefine vars
		$horizontal = (string) $horizontal;
		$vertical = (string) $vertical;

		// validate horizontal
		if(SpoonFilter::getValue($horizontal, array('left', 'center', 'right'), '') == '')
		{
			if($this->strict) throw new SpoonImageException('The horizontal crop-position ('.$horizontal.') isn\'t valid.');
			return false;
		}

		// validte vertical
		if(SpoonFilter::getValue($vertical, array('top', 'middle', 'bottom'), '') == '')
		{
			if($this->strict) throw new SpoonImageException('The vertical crop-position ('.$vertical.') isn\'t valid.');
			return false;
		}

		// set properties
		$this->cropPositionHorizontal = $horizontal;
		$this->cropPositionVertical = $vertical;
	}


	/**
	 * Enables the Force aspect ratio
	 *
	 * @return	void
	 * @param	bool[optional]  $on
	 */
	public function setForceOriginalAspectRatio($on = true)
	{
		$this->forceOriginalAspectRatio = (bool) $on;
	}


	/**
	 * Set the strict option
	 *
	 * @return	void
	 * @param	bool[optional] $on
	 */
	public function setStrict($on = true)
	{
		$this->strict = (bool) $on;
	}
}

?>