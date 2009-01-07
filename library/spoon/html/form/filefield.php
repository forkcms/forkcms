<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.be
 *
 * @package			html
 * @subpackage		form
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @author			Tijs Verkoyen <tijs@spoon-library.be>
 * @since			0.1.1
 */


/** SpoonFormElement class */
require_once 'spoon/html/form/visual_element.php';

/** SpoonFileField class */
require_once 'spoon/filesystem/file.php';


/**
 * Create an html filefield
 *
 * @package			html
 * @subpackage		form
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @since			0.1.1
 */
class SpoonFileField extends SpoonVisualFormElement
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
	 * File extension
	 *
	 * @var	string
	 */
	private $extension;


	/**
	 * Filename (without extension)
	 *
	 * @var	string
	 */
	private $filename;


	/**
	 * Class constructor
	 *
	 * @return	void
	 * @param	string $namee
	 */
	public function __construct($name, $class = 'input-filefield', $classError = 'input-filefield-error')
	{
		// set name & id
		$this->setName($name);
		$this->setId($name);

		// custom optional fields
		if($class !== null) $this->setClass($class);
		if($classError !== null) $this->setClassOnError($classError);
	}


	/**
	 * Adds an error to the error stack
	 *
	 * @return	void
	 * @param	string $error
	 */
	public function addError($error)
	{
		$this->errors .= (string) $error;
	}


	/**
	 * Retrieves the class based on the errors status
	 *
	 * @return	string
	 */
	public function getClassAsHtml()
	{
		// default value
		$value = '';

		// has errors
		if($this->errors != '')
		{
			// class & classOnError defined
			if($this->class != '' && $this->classError != '') $value = ' class="'. $this->class .' '. $this->classError .'"';

			// only class defined
			elseif($this->class != '') $value = ' class="'. $this->class .'"';

			// only error defined
			elseif($this->classError != '') $value = ' class="'. $this->classError .'"';
		}

		// no errors
		else
		{
			// class defined
			if($this->class != '') $value = ' class="'. $this->class .'"';
		}

		return $value;
	}


	/**
	 * Retrieve the class on error
	 *
	 * @return	string
	 */
	public function getClassOnError()
	{
		return $this->classError;
	}


	/**
	 * Retrieve the errors
	 *
	 * @return	string
	 */
	public function getErrors()
	{
		return $this->errors;
	}


	/**
	 * Retrieve the extension of the uploaded file
	 *
	 * @return	string
	 * @param	bool[optional] $lowercase
	 */
	public function getExtension($lowercase = true)
	{
		return $this->isFilled() ? (SpoonFile::getExtension($_FILES[$this->getName()]['name'], $lowercase)) : '';
	}


	/**
	 * Retrieve the filename of the uploade file
	 *
	 * @return	string
	 * @param	bool[optional] $includeExtension
	 */
	public function getFileName($includeExtension = true)
	{
		if($this->isFilled()) return (!$includeExtension) ? str_replace(SpoonFile::getExtension($_FILES[$this->getName()]['name']), '', $_FILES[$this->getName()]['name']) : $_FILES[$this->getName()]['name'];
		return '';
	}


	/**
	 * Retrieve the filesize of the file in a specified unit
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
			$size = $_FILES[$this->getName()]['size'];

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
	 * Get the temporary filename
	 *
	 * @return	string
	 */
	public function getTempFileName()
	{
		return $this->isFilled() ? (string) $_FILES[$this->getName()]['tmp_name'] : '';
	}


	/**
	 * Checks if the extension is allowed
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
			$return = in_array(strtolower(SpoonFile::getExtension($_FILES[$this->getName()]['name'])), $extensions);

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
	 * Checks of the filesize is greater, equal or smaller than the given number + units
	 *
	 * @return	bool
	 * @param	int $size
	 * @param	string[optional] $unit
	 * @param	string[optional] $operator
	 * @param	string[optional] $error
	 */
	public function isFileSize($size, $unit = 'kb', $operator = 'smaller', $error = null)
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
			if($operator == 'greater' && $actualSize == $size) return true;
		}

		// has error
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks for a valid file name (including dots but no slashes and other forbidden characters)
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
	 * Checks if this field was submitted & filled
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
			if(isset($_FILES[$this->getName()]) && $_FILES[$this->getName()]['error'] == 0 && $_FILES[$this->getName()] != '') $hasError = false;
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
	 * Attemps to move the uploaded file to the new location
	 *
	 * @return	bool
	 * @param	string $path
	 * @param	int[optional] $chmod
	 */
	public function moveFile($path, $chmod = 0755)
	{
		// move the file
		$return = @move_uploaded_file($_FILES[$this->getName()]['tmp_name'], (string) $path);

		// chmod file
		SpoonFile::chmod($path, $chmod);

		// return move file status
		return $return;
	}


	/**
	 * Parses the html for this filefield
	 *
	 * @return	string
	 * @param	SpoonTemplate[optional] $template
	 */
	public function parse(SpoonTemplate $template = null)
	{
		// name is required
		if($this->getName() == '') throw new SpoonFormException('A name is required for a file field. Please provide a name.');

		// start html generation
		$output = '<input type="file" id="'. $this->getId() .'" name="'. $this->getName() .'"';

		// class / classOnError
		if($this->getClassAsHtml() != '') $output .= $this->getClassAsHtml();

		// style attribute
		if($this->style !== null) $output .= ' style="'. $this->getStyle() .'"';

		// tabindex
		if($this->tabindex !== null) $output .= ' tabindex="'. $this->getTabIndex() .'"';

		// add javascript methods
		if($this->getJavascriptAsHtml() != '') $output .= $this->getJavascriptAsHtml();

		// disabled
		if($this->disabled) $output .= ' disabled="disabled"';

		// end html
		$output .= ' />';

		// parse to template
		if($template !== null)
		{
			$template->assign('file'. SpoonFilter::toCamelCase($this->name), $output);
			$template->assign('file'. SpoonFilter::toCamelCase($this->name) .'Error', ($this->errors!= '') ? '<span class="form-error">'. $this->errors .'</span>' : '');
		}

		// cough it up
		return $output;
	}


	/**
	 * Set the class on error
	 *
	 * @return	void
	 * @param	string $class
	 */
	public function setClassOnError($class)
	{
		$this->classError = (string) $class;
	}


	/**
	 * Overwrites the error stack
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