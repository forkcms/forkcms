<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.com
 *
 * @package		spoon
 * @subpackage	form
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @author		P. Juchtmans <per@dubgeiser.net>
 * @since		0.1.1
 */


/**
 * Create an html filefield specific for images
 *
 * @package		spoon
 * @subpackage	form
 *
 *
 * @author		Tijs Verkoyen <tijs@spoon-library.com>
 * @since		1.1.3
 */
class SpoonFormImage extends SpoonFormFile
{
	/**
	 * @var array Will hold properties of the image (from getimagesize())
	 * @see http://www.php.net/getimagesize
	 */
	private $properties;


	/**
	 * Constructor.
	 *
	 * @param	string $name					The name.
	 * @param	string[optional] $class			The CSS-class to be used.
	 * @param	string[optional] $classError	The CSS-class to be used when there is an error.
	 * @see		SpoonFormFile::__construct()
	 */
	public function __construct($name, $class = 'inputFilefield', $classError = 'inputFilefieldError')
	{
		// call the parent
		parent::__construct($name, $class, $classError);

		// is the form submitted and the file is uploaded?
		if($this->isSubmitted() && is_uploaded_file($this->getTempFileName())) $this->properties = @getimagesize($this->getTempFileName());

		// fallback
		else $this->properties = false;
	}


	/**
	 * Creates a thumbnail from this field
	 *
	 * @return	bool
	 * @param	string $filename							The name of the file.
	 * @param	int[optional] $width						The width.
	 * @param	int[optional] $height						The height.
	 * @param	bool[optional] $allowEnlargement			Is enlargment allowed?
	 * @param	bool[optional] $forceOriginalAspectRatio	Should we force the original aspect ratio?
	 * @param	int[optional] $quality						The quality, only applies on jpg-images.
	 */
	public function createThumbnail($filename, $width = null, $height = null, $allowEnlargement = false, $forceOriginalAspectRatio = true, $quality = 100)
	{
		$thumbnail = new SpoonThumbnail($this->getTempFileName(), $width, $height, true);
		$thumbnail->setAllowEnlargement($allowEnlargement);
		$thumbnail->setForceOriginalAspectRatio($forceOriginalAspectRatio);
		return $thumbnail->parseToFile($filename, $quality);
	}


	/**
	 * Retrieve the extension of the uploaded file (based on the MIME-type).
	 *
	 * @return	string
	 * @param	bool[optional] $lowercase	Should the extensions be returned in lowercase?
	 */
	public function getExtension($lowercase = true)
	{
		if($this->isSubmitted())
		{
			// validate properties
			if($this->properties !== false)
			{
				// get extension
				$extension = image_type_to_extension($this->properties[2], false);

				// cleanup
				if($extension == 'jpeg') $extension = 'jpg';

				// return
				return ((bool) $lowercase) ? strtolower($extension) : $extension;
			}
		}

		// fallback
		return '';
	}


	/**
	 * Return the height of the image.
	 *
	 * @return	int
	 */
	public function getHeight()
	{
		// not submitted
		if(!$this->isSubmitted()) throw new SpoonException('Cannot get height if image is not uploaded.');

		// return
		if($this->properties) return $this->properties[1];
	}


	/**
	 * Return the width of the image.
	 *
	 * @return	int
	 */
	public function getWidth()
	{
		// not submitted
		if(!$this->isSubmitted()) throw new SpoonException('Cannot get width if image is not uploaded.');

		// return
		if($this->properties) return $this->properties[0];
	}


	/**
	 * Checks if this field was submitted an if it is an image check if the dimensions are ok,
	 * if the submitted file wasn't an image it will return false.
	 *
	 * @return	bool
	 * @param	int $width					The minimum width.
	 * @param	int $height					The minimum height.
	 * @param	string[optional] $error		The errormessage to set.
	 */
	public function hasMinimumDimensions($width, $height, $error = null)
	{
		// default error
		$hasError = true;

		// form submitted
		if($this->isSubmitted())
		{
			// valid properties
			if($this->properties !== false)
			{
				// redefine
				$actualWidth = (int) $this->properties[0];
				$actualHeight = (int) $this->properties[1];

				// validate width and height
				if($actualWidth >= $width && $actualHeight >= $height) $hasError = false;
			}
		}

		// has erorr?
		if($hasError)
		{
			if($error !== null) $this->setError($error);
			return false;
		}

		return true;
	}


	/**
	 * Is this image square?
	 *
	 * @return	bool
	 * @param	string[optional] $error		The errormessage to set.
	 */
	public function isSquare($error = null)
	{
		// no properties?
		if(!$this->properties) return false;

		// is it a square image?
		$isSquare = (is_int($this->getHeight()) && is_int($this->getWidth()) && $this->getHeight() == $this->getWidth());

		// set error if needed
		if(!$isSquare && $error) $this->setError($error);

		// return
		return $isSquare;
	}
}
