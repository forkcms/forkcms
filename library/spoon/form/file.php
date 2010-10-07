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
 * Create an html filefield
 *
 * @package		spoon
 * @subpackage	form
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		0.1.1
 */
class SpoonFormFile extends SpoonFormAttributes
{
	/**
	 * Class attribute on error
	 *
	 * @var	string
	 */
	protected $classError;


	/**
	 * Errors stack
	 *
	 * @var	string
	 */
	protected $errors;


	/**
	 * Filename (without extension)
	 *
	 * @var	string
	 */
	private $filename;


	/**
	 * Class constructor.
	 *
	 * @return	void
	 * @param	string $name
	 * @param	string[optional] $class
	 * @param	string[optional] $classError
	 */
	public function __construct($name, $class = 'inputFilefield', $classError = 'inputFilefieldError')
	{
		// set name & id
		$this->attributes['id'] = SpoonFilter::toCamelCase((string) $name, '_', true);
		$this->attributes['name'] = (string) $name;

		// custom optional fields
		$this->attributes['class'] = (string) $class;
		$this->classError = (string) $classError;
	}


	/**
	 * Adds an error to the error stack.
	 *
	 * @return	void
	 * @param	string $error
	 */
	public function addError($error)
	{
		$this->errors .= (string) $error;
	}


	/**
	 * Retrieves the class based on the errors status.
	 *
	 * @return	string
	 */
	protected function getClassHTML()
	{
		// default value
		$value = '';

		// has errors
		if($this->errors != '')
		{
			// class & classOnError defined
			if($this->attributes['class'] != '' && $this->classError != '') $value = ' class="'. $this->attributes['class'] .' '. $this->classError .'"';

			// only class defined
			elseif($this->attributes['class'] != '') $value = ' class="'. $this->attributes['class'] .'"';

			// only error defined
			elseif($this->classError != '') $value = ' class="'. $this->classError .'"';
		}

		// no errors
		else
		{
			// class defined
			if($this->attributes['class'] != '') $value = ' class="'. $this->attributes['class'] .'"';
		}

		return $value;
	}


	/**
	 * Retrieve the errors.
	 *
	 * @return	string
	 */
	public function getErrors()
	{
		return $this->errors;
	}


	/**
	 * Retrieve the extension of the uploaded file.
	 *
	 * @return	string
	 * @param	bool[optional] $lowercase
	 */
	public function getExtension($lowercase = true)
	{
		return $this->isFilled() ? (SpoonFile::getExtension($_FILES[$this->attributes['name']]['name'], $lowercase)) : '';
	}


	/**
	 * Retrieve the filename of the uploade file.
	 *
	 * @return	string
	 * @param	bool[optional] $includeExtension
	 */
	public function getFileName($includeExtension = true)
	{
		if($this->isFilled()) return (!$includeExtension) ? substr($_FILES[$this->attributes['name']]['name'], 0, strripos($_FILES[$this->attributes['name']]['name'], '.'. SpoonFile::getExtension($_FILES[$this->attributes['name']]['name'], false))) : $_FILES[$this->attributes['name']]['name'];
		return '';
	}


	/**
	 * Retrieve the filesize of the file in a specified unit.
	 *
	 * @return	int
	 * @param	string[optional] $unit
	 * @param	int[optional] $precision
	 */
	public function getFileSize($unit = 'kb', $precision = null)
	{
		if($this->isFilled())
		{
			// redefine unit
			$unit = SpoonFilter::getValue(strtolower($unit), array('b', 'kb', 'mb', 'gb'), 'kb');

			// fetch size
			$size = $_FILES[$this->attributes['name']]['size'];

			// redefine prection
			if($precision !== null) $precision = (int) $precision;

			// bytes
			if($unit == 'b') return $size;

			// kilobytes
			if($unit == 'kb') return round(($size / 1024), $precision);

			// megabytes
			if($unit == 'mb') return round(($size / 1024 / 1024), $precision);

			// gigabytes
			if($unit == 'gb') return round(($size / 1024 / 1024 / 1024), $precision);
		}

		return 0;
	}


	/**
	 * Get the temporary filename.
	 *
	 * @return	string
	 */
	public function getTempFileName()
	{
		return $this->isFilled() ? (string) $_FILES[$this->attributes['name']]['tmp_name'] : '';
	}


	/**
	 * Checks if the extension is allowed.
	 *
	 * @return	bool
	 * @param	array $extensions
	 * @param	string[optional] $error
	 */
	public function isAllowedExtension(array $extensions, $error = null)
	{
		// file has been uploaded
		if($this->isFilled())
		{
			// search for extension
			$return = in_array(strtolower(SpoonFile::getExtension($_FILES[$this->attributes['name']]['name'])), $extensions);

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


	/**
	 * Checks of the filesize is greater, equal or smaller than the given number + units.
	 *
	 * @return	bool
	 * @param	int $size
	 * @param	string[optional] $unit
	 * @param	string[optional] $operator
	 * @param	string[optional] $error
	 */
	public function isFilesize($size, $unit = 'kb', $operator = 'smaller', $error = null)
	{
		// file has been uploaded
		if($this->isFilled())
		{
			// define size
			$actualSize = $this->getFileSize($unit, 0);

			// operator
			$operator = SpoonFilter::getValue(strtolower($operator), array('smaller', 'equal', 'greater'), 'smaller');

			// smaller
			if($operator == 'smaller' && $actualSize < $size) return true;

			// equal
			if($operator == 'equal' && $actualSize == $size) return true;

			// greater
			if($operator == 'greater' && $actualSize > $size) return true;
		}

		// has error
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks for a valid file name (including dots but no slashes and other forbidden characters).
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isFilename($error = null)
	{
		// correct filename
		if($this->isFilled() && SpoonFilter::isFilename($this->getFileName())) return true;

		// has error
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks if this field was submitted & filled.
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isFilled($error = null)
	{
		// default error
		$hasError = true;

		// form submitted
		if($this->isSubmitted())
		{
			// submitted, no errors & has a name!
			if(isset($_FILES[$this->attributes['name']]) && $_FILES[$this->attributes['name']]['error'] == 0 && $_FILES[$this->attributes['name']] != '') $hasError = false;
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
	 * Attemps to move the uploaded file to the new location.
	 *
	 * @return	bool
	 * @param	string $path
	 * @param	int[optional] $chmod
	 */
	public function moveFile($path, $chmod = 0755)
	{
		// move the file
		$return = @move_uploaded_file($_FILES[$this->attributes['name']]['tmp_name'], (string) $path);

		// chmod file
		@chmod($path, $chmod);

		// return move file status
		return $return;
	}


	/**
	 * Parses the html for this filefield.
	 *
	 * @return	string
	 * @param	SpoonTemplate[optional] $template
	 */
	public function parse(SpoonTemplate $template = null)
	{
		// name is required
		if($this->attributes['name'] == '') throw new SpoonFormException('A name is required for a file field. Please provide a name.');

		// start html generation
		$output = '<input type="file"';

		// add attributes
		$output .= $this->getAttributesHTML(array('[id]' => $this->attributes['id'], '[name]' => $this->attributes['name'])) .' />';

		// parse to template
		if($template !== null)
		{
			$template->assign('file'. SpoonFilter::toCamelCase($this->attributes['name']), $output);
			$template->assign('file'. SpoonFilter::toCamelCase($this->attributes['name']) .'Error', ($this->errors!= '') ? '<span class="formError">'. $this->errors .'</span>' : '');
		}

		return $output;
	}


	/**
	 * Set the class on error.
	 *
	 * @return	void
	 * @param	string $class
	 */
	public function setClassOnError($class)
	{
		$this->classError = (string) $class;
	}


	/**
	 * Overwrites the error stack.
	 *
	 * @return	void
	 * @param	string $error
	 */
	public function setError($error)
	{
		$this->errors = (string) $error;
	}
}

?>