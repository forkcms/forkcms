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
			// get image properties
			$properties = @getimagesize($_FILES[$this->attributes['name']]['tmp_name']);

			// validate properties
			if($properties !== false)
			{
				// get extension
				$extension = image_type_to_extension($properties[2], false);

				// cleanup
				if($extension == 'jpeg') $extension = 'jpg';

				// return
				return ((bool) $lowercase) ? strtolower($extension) : $extension;
			}

			// no image
			return '';
		}

		// fallback
		return '';
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
			// get image properties
			$properties = @getimagesize($_FILES[$this->attributes['name']]['tmp_name']);

			// valid properties
			if($properties !== false)
			{
				// redefine
				$actualWidth = (int) $properties[0];
				$actualHeight = (int) $properties[1];

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
}

?>