<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.com
 *
 * @package		spoon
 * @subpackage	thumbnail
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		1.0.0
 */


/**
 * This class is used to create thumbnails
 *
 * @package		spoon
 * @subpackage	thumbnail
 *
 *
 * @author		Tijs Verkoyen <tijs@spoon-library.com>
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		1.0.0
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
	private $filename;


	/**
	 * Should we respect the original aspect ratio?
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
	private $strict = true;


	/**
	 * Default constructor.
	 *
	 * @param	string $filename		The path to the source-image.
	 * @param	int[optional] $width	The required width, if not provided it will be calculated based on the height.
	 * @param	int[optional] $height	The required height, if not provided it will be calculated based on the width.
	 * @param	bool[optional] $strict	Should strict-mode be activated?
	 */
	public function __construct($filename, $width = null, $height = null, $strict = true)
	{
		// check if gd is available
		if(!extension_loaded('gd')) throw new SpoonThumbnailException('GD2 isn\'t loaded. Contact your server-admin to enable it.');

		// redefine vars
		$filename = (string) $filename;
		if($width != null) $width = (int) $width;
		if($height != null) $height = (int) $height;

		// set strict
		$this->strict = (bool) $strict;

		// validate
		if(!SpoonFile::exists($filename)) throw new SpoonThumbnailException('The sourcefile "' . $filename . '" couldn\'t be found.');

		// set properties
		$this->filename = $filename;
		$this->width = $width;
		$this->height = $height;
	}


	/**
	 * Check if file is supported.
	 *
	 * @return	bool				True if the file is supported, false if not.
	 * @param	string $filename	The path to the file tp check.
	 */
	public static function isSupportedFileType($filename)
	{
		// get watermarkfile properties
		list($width, $height, $type) = @getimagesize($filename);

		// create image from sourcefile
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
	 * Outputs the image as png to the browser.
	 *
	 * @param	bool[optional] $headers		Should the headers be send? This is a usefull when you're debugging.
	 */
	public function parse($headers = true)
	{
		// set headers
		if($headers) SpoonHTTP::setHeaders('Content-type: image/png');

		// get current dimensions
		$imageProperties = @getimagesize($this->filename);

		// validate imageProperties
		if($imageProperties === false) throw new SpoonThumbnailException('The sourcefile "' . $this->filename . '" could not be found.');

		// set current dimensions
		$currentWidth = (int) $imageProperties[0];
		$currentHeight = (int) $imageProperties[1];
		$currentType = (int) $imageProperties[2];
		$currentMime = (string) $imageProperties['mime'];

		// resize image
		$this->resizeImage($currentWidth, $currentHeight, $currentType, $currentMime);

		// output image
		$success = @imagepng($this->image);

		// validate
		if(!$success) throw new SpoonThumbnailException('Something went wrong while outputting the image.');

		// cleanup the memory
		@imagedestroy($this->image);
	}


	/**
	 * Saves the image to a file (quality is only used for jpg images).
	 *
	 * @return	bool						True if the image was saved, false if not.
	 * @param	string $filename			The path where the image should be saved.
	 * @param	int[optional] $quality		The quality to use (only applies on jpg-images).
	 * @param	int[optional] $chmod		Mode that should be applied on the file.
	 */
	public function parseToFile($filename, $quality = 100, $chmod = 0666)
	{
		// redefine vars
		$filename = (string) $filename;
		$quality = (int) $quality;

		//
		if(@is_writable(dirname($filename)) !== true)
		{
			// does the folder exist? if not, try to create
			if(!SpoonDirectory::create(dirname($filename)))
			{
				if($this->strict) throw new SpoonThumbnailException('The destination-path should be writable.');
				return false;
			}
		}

		// get extension
		$extension = SpoonFile::getExtension($filename);

		// invalid quality
		if(!SpoonFilter::isBetween(1, 100, $quality))
		{
			// strict?
			if($this->strict) throw new SpoonThumbnailException('The quality should be between 1 - 100');
			return false;
		}

		// invalid extension
		if(SpoonFilter::getValue($extension, array('gif', 'jpeg', 'jpg', 'png'), '') == '')
		{
			if($this->strict) throw new SpoonThumbnailException('Only gif, jpeg, jpg or png are allowed types.');
			return false;
		}

		// get current dimensions
		$imageProperties = @getimagesize($this->filename);

		// validate imageProperties
		if($imageProperties === false)
		{
			// strict?
			if($this->strict) throw new SpoonThumbnailException('The sourcefile "' . $this->filename . '" could not be found.');
			return false;
		}

		// set current dimensions
		$currentWidth = (int) $imageProperties[0];
		$currentHeight = (int) $imageProperties[1];
		$currentType = (int) $imageProperties[2];
		$currentMime = (string) $imageProperties['mime'];

		// file is the same?
		if(($currentType == IMAGETYPE_GIF && $extension == 'gif') || ($currentType == IMAGETYPE_JPEG && in_array($extension, array('jpg', 'jpeg'))) || ($currentType == IMAGETYPE_PNG && $extension == 'png'))
		{
			if($currentWidth == $this->width && $currentHeight == $this->height)
			{
				return SpoonDirectory::copy($this->filename, $filename, true, true, $chmod);
			}
		}

		// resize image
		$this->resizeImage($currentWidth, $currentHeight, $currentType, $currentMime);

		// output to file
		switch(strtolower($extension))
		{
			case 'gif':
				$return = @imagegif($this->image, $filename);
			break;

			case 'jpeg':
			case 'jpg':
				$return = @imagejpeg($this->image, $filename, $quality);
			break;

			case 'png':
				$return = @imagepng($this->image, $filename);
			break;
		}

		// chmod
		@chmod($filename, $chmod);

		// cleanup memory
		@imagedestroy($this->image);

		// return success
		return (bool) $return;
	}


	/**
	 * This internal function will resize/crop the image.
	 *
	 * @param	int $currentWidth		Original width.
	 * @param	int $currentHeight		Original height.
	 * @param	int $currentType		Current type of image.
	 * @param	string $currentMime		Current mime-type.
	 */
	private function resizeImage($currentWidth, $currentHeight, $currentType, $currentMime)
	{
		// check if needed dimensions are present
		if(!$this->forceOriginalAspectRatio) $this->resizeImageWithoutForceAspectRatio($currentWidth, $currentHeight, $currentType, $currentMime);

		// FAR is on
		else $this->resizeImageWithForceAspectRatio($currentWidth, $currentHeight, $currentType, $currentMime);
	}


	/**
	 * Resize the image with Force Aspect Ratio.
	 *
	 * @param	int $currentWidth		Original width.
	 * @param	int $currentHeight		Original height.
	 * @param	int $currentType		Current type of image.
	 * @param	string $currentMime		Current mime-type.
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
			else throw new SpoonThumbnailException('No width or height specified.');
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
			else throw new SpoonThumbnailException('No width or height specified.');
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
			else throw new SpoonThumbnailException('No width or height specified.');
		}

		// check if we stay within the borders
		if($this->width !== null && $this->height !== null)
		{
			if($newWidth > $this->width)
			{
				// width is specified
				$newWidth = $this->width;

				// calculate new height
				$newHeight = (int) floor($currentHeight * ($this->width / $currentWidth));
			}

			if($newHeight > $this->height)
			{
				// height is specified
				$newHeight = $this->height;

				// calculate new width
				$newWidth = (int) floor($currentWidth * ($this->height / $currentHeight));
			}
		}

		// read current image
		switch($currentType)
		{
			case IMG_GIF:
				$currentImage = @imagecreatefromgif($this->filename);
			break;

			case IMG_JPG:
				$currentImage = @imagecreatefromjpeg($this->filename);
			break;

			case 3:
			case IMG_PNG:
				$currentImage = @imagecreatefrompng($this->filename);
			break;

			default:
				throw new SpoonThumbnailException('The file you specified "' . $currentMime . '" is not supported. Only gif, jpeg, jpg and png are supported.');
		}

		// validate image
		if($currentImage === false) throw new SpoonThumbnailException('The file you specified is corrupt.');

		// create image resource
		$this->image = @imagecreatetruecolor($newWidth, $newHeight);

		// validate
		if($this->image === false) throw new SpoonThumbnailException('Could not create new image.');

		// set transparent
		@imagealphablending($this->image, false);

		// transparency supported
		if(in_array($currentType, array(IMG_GIF, 3, IMG_PNG)))
		{
			// get transparent color
			$colorTransparent = @imagecolorallocatealpha($this->image, 0, 0, 0, 127);

			// any color found?
			if($colorTransparent !== false)
			{
				@imagefill($this->image, 0, 0, $colorTransparent);
				@imagesavealpha($this->image, true);
			}
		}

		// resize
		$success = @imagecopyresampled($this->image, $currentImage, 0, 0, 0, 0, $newWidth, $newHeight, $currentWidth, $currentHeight);

		// image creation fail
		if(!$success)
		{
			if($this->strict) throw new SpoonThumbnailException('Something went wrong while trying to resize the image.');
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

				// validate transparent color
				if($transparentColor !== false)
				{
					// get color
					$transparentIndex = @imagecolorallocate($this->image, $transparentColor['red'], $transparentColor['green'], $transparentColor['blue']);

					// fill
					if($transparentIndex !== false)
					{
						@imagefill($this->image, 0, 0, $transparentIndex);
						@imagecolortransparent($this->image, $transparentIndex);
					}
				}
			}
		}
	}


	/**
	 * Resize the image without Force Aspect Ratio.
	 *
	 * @param	int $currentWidth		Original width.
	 * @param	int $currentHeight		Original height.
	 * @param	int $currentType		Current type of image.
	 * @param	string $currentMime		Current mime-type.
	 */
	private function resizeImageWithoutForceAspectRatio($currentWidth, $currentHeight, $currentType, $currentMime)
	{
		// validate
		if($this->width === null || $this->height === null) throw new SpoonThumbnailException('If forceAspectRatio is false you have to specify width and height.');

		// set new size
		$newWidth = $this->width;
		$newHeight = $this->height;

		// read current image
		switch($currentType)
		{
			case IMG_GIF:
				$currentImage = @imagecreatefromgif($this->filename);
			break;

			case IMG_JPG:
				$currentImage = @imagecreatefromjpeg($this->filename);
			break;

			case 3:
			case IMG_PNG:
				$currentImage = @imagecreatefrompng($this->filename);
			break;

			default:
				throw new SpoonThumbnailException('The file you specified "' . $currentMime . '" is not supported. Only gif, jpeg, jpg and png are supported.');
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
			if($this->strict) throw new SpoonThumbnailException('Something went wrong while resizing the image.');
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
			if($this->strict) throw new SpoonThumbnailException('The specified width/height is larger then the original width/height. Please enable allowEnlargement.');
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
			if($this->strict) throw new SpoonThumbnailException('Something went wrong while resizing the image.');
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
	 * set the allowEnlargement, default is false.
	 *
	 * @param	bool[optional] $on	May the original image be enlarged.
	 */
	public function setAllowEnlargement($on = false)
	{
		$this->allowEnlargement = (bool) $on;
	}


	/**
	 * Sets the horizontal and vertical cropposition.
	 *
	 * @return	mixed							In strict-mode it wil return false on errors.
	 * @param	string[optional] $horizontal	The horizontal crop position, possible values are: left, center, right.
	 * @param	string[optional] $vertical		The vertical crop position, possible values are: top, middle, bottom.
	 */
	public function setCropPosition($horizontal = 'center', $vertical = 'middle')
	{
		// redefine vars
		$horizontal = (string) $horizontal;
		$vertical = (string) $vertical;

		// validate horizontal
		if(SpoonFilter::getValue($horizontal, array('left', 'center', 'right'), '') == '')
		{
			if($this->strict) throw new SpoonThumbnailException('The horizontal crop-position "' . $horizontal . '" isn\'t valid.');
			return false;
		}

		// validte vertical
		if(SpoonFilter::getValue($vertical, array('top', 'middle', 'bottom'), '') == '')
		{
			if($this->strict) throw new SpoonThumbnailException('The vertical crop-position "' . $vertical . '" isn\'t valid.');
			return false;
		}

		// set properties
		$this->cropPositionHorizontal = $horizontal;
		$this->cropPositionVertical = $vertical;
	}


	/**
	 * Enables the Force aspect ratio.
	 *
	 * @param	bool[optional] $on	Should the original aspect ratio be respected?
	 */
	public function setForceOriginalAspectRatio($on = true)
	{
		$this->forceOriginalAspectRatio = (bool) $on;
	}


	/**
	 * Set the strict option.
	 *
	 * @param	bool[optional] $on	Should strict-mode be enabled?
	 */
	public function setStrict($on = true)
	{
		$this->strict = (bool) $on;
	}
}


/**
 * This exception is used to handle image related exceptions.
 *
 * @package		spoon
 * @subpackage	thumbnail
 *
 *
 * @author		Tijs Verkoyen <tijs@spoon-library.com>
 * @since		1.0.0
 */
class SpoonThumbnailException extends SpoonException {}
