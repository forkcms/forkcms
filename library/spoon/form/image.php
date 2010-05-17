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
 * @author 		Tijs Verkoyen <tijs@spoon-library.com>
 * @author		Dave Lens <dave@spoon-library.com>
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
	 * Retrieve the extension of the uploaded file (based on the MIME-type).
	 *
	 * @return	string
	 * @param	bool[optional] $lowercase
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
	 * @param	int $width
	 * @param	int $height
	 * @param	string[optional] $error
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


	/**
	 * Checks if the mime-type is allowed.
	 * @see	http://www.w3schools.com/media/media_mimeref.asp
	 *
	 * @return	bool
	 * @param	array $allowedTypes
	 * @param	string[optional] $error
	 */
	public function isAllowedMimeType(array $allowedTypes, $error = null)
	{
		// file has been uploaded
		if($this->isFilled())
		{
			// get image properties
			$properties = @getimagesize($_FILES[$this->attributes['name']]['tmp_name']);

			// invalid properties
			if($properties === false) $return = false;

			// search for mime-type
			else $return = in_array($properties['mime'], $allowedTypes);

			// add error if needed
			if(!$return && $error !== null) $this->setError($error);

			// return
			return $return;
		}

		// no file uploaded
		else
		{
			// add error if needed
			if($error !== null) $this->setError($error);

			// return
			return false;
		}
	}
}

?>