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


/** SpoonInputField class */
require_once 'spoon/html/form/input_field.php';


/**
 * Create an html textarea
 *
 * @package			html
 * @subpackage		form
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @since			0.1.1
 */
class SpoonTextArea extends SpoonInputField
{
	/**
	 * Number of columns
	 *
	 * @var	int
	 */
	private $cols = 62;


	/**
	 * Is html allowed?
	 *
	 * @var	bool
	 */
	private $isHtml = false;


	/**
	 * Number of rows
	 *
	 * @var	int
	 */
	private $rows = 5;


	/**
	 * Class constructor
	 *
	 * @return	void
	 * @param	string $name
	 * @param	string[optional] $value
	 * @param	string[optional] $class
	 * @param	string[optional] $classError
	 * @param	bool[optional] $html
	 */
	public function __construct($name, $value = null, $class = 'input-textarea', $classError = 'input-textarea-error', $html = false)
	{
		// obligated fields
		$this->setId($name);
		$this->setName($name);

		// custom optional fields
		$this->setValue($value);
		if($class !== null) $this->setClass($class);
		if($classError !== null) $this->setClassOnError($classError);
		$this->isHtml($html);
	}


	/**
	 * Retrieve the number of cols
	 *
	 * @return	mixed
	 */
	public function getCols()
	{
		return $this->cols;
	}


	/**
	 * This method is not to be used with this class
	 *
	 * @return	void
	 */
	public function getMaxlength()
	{
		throw new SpoonFormException('This method is not to be used with the SpoonTextArea class.');
	}


	/**
	 * Retrieve the number of rows
	 *
	 * @return	mixed
	 */
	public function getRows()
	{
		return $this->rows;
	}


	/**
	 * Retrieve the initial or submitted value
	 *
	 * @return	string
	 * @param	bool[optional] $allowHtml
	 */
	public function getValue($allowHtml = false)
	{
		// html & default value
		$allowHtml = (bool) $allowHtml;
		$value = ($this->isHtml) ? SpoonFilter::htmlentities($this->value) : $this->value;

		// added to form
		if($this->isSubmitted())
		{
			// post/get data
			$data = $this->getMethod(true);

			// submitted by post (may be empty)
			if(isset($data[$this->getName()]))
			{
				// value
				$value = $data[$this->getName()];

				// html allowed?
				if(!$allowHtml) $value = SpoonFilter::htmlentities($value);
			}
		}

		return $value;
	}


	/**
	 * Checks if this field contains only letters a-z and A-Z
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isAlphabetical($error = null)
	{
		// post/get data
		$data = $this->getMethod(true);

		// validate
		if(!SpoonFilter::isAlphabetical($data[$this->getName()]))
		{
			if($error !== null) $this->setError($error);
			return false;
		}

		return true;
	}


	/**
	 * Checks if this field only contains letters & numbers (without spaces)
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isAlphaNumeric($error = null)
	{
		// post/get data
		$data = $this->getMethod(true);

		// validate
		if(!SpoonFilter::isAlphaNumeric($data[$this->getName()]))
		{
			if($error !== null) $this->setError($error);
			return false;
		}

		return true;
	}


	/**
	 * Checks if this field was submitted & filled
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isFilled($error = null)
	{
		// post/get data
		$data = $this->getMethod(true);

		// validate
		if(!(isset($data[$this->getName()]) && trim($data[$this->getName()]) != ''))
		{
			if($error !== null) $this->setError($error);
			return false;
		}

		return true;
	}


	/**
	 * Make spoon aware that this field contains html
	 *
	 * @return	void
	 * @param	bool[optional] $on
	 */
	public function isHtml($on = true)
	{
		$this->isHtml = (bool) $on;
	}


	/**
	 * Checks if this field's length is less (or equal) than the given maximum
	 *
	 * @return	bool
	 * @param	int $maximum
	 * @param	string[optional] $error
	 */
	public function isMaximumCharacters($maximum, $error = null)
	{
		// post/get data
		$data = $this->getMethod(true);

		// validate
		if(!SpoonFilter::isMaximumCharacters($maximum, $data[$this->getName()]))
		{
			if($error !== null) $this->setError($error);
			return false;
		}

		return true;
	}


	/**
	 * Checks if this field's length is more (or equal) than the given minimum
	 *
	 * @return	bool
	 * @param	int $minimum
	 * @param	string[optional] $error
	 */
	public function isMinimumCharacters($minimum, $error = null)
	{
		// post/get data
		$data = $this->getMethod(true);

		// validate
		if(!SpoonFilter::isMinimumCharacters($minimum, $data[$this->getName()]))
		{
			if($error !== null) $this->setError($error);
			return false;
		}

		return true;
	}


	/**
	 * Checks if this field contains any string that doesn't have control characters (ASCII 0 - 31) but spaces are allowed
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isString($error = null)
	{
		// post/get data
		$data = $this->getMethod(true);

		// validate
		if(!SpoonFilter::isString($data[$this->getName()]))
		{
			if($error !== null) $this->setError($error);
			return false;
		}

		return true;
	}


	/**
	 * Parses the html for this textarea
	 *
	 * @return	string
	 * @param	SpoonTemplate[optional] $template
	 */
	public function parse(SpoonTemplate $template = null)
	{
		// name is required
		if($this->getName() == '') throw new SpoonFormException('A name is requird for a textarea. Please provide a valid name.');

		// start html generation
		$output = '<textarea id="'. $this->getId() .'" name="'. $this->getName() .'"';

		// class / classOnError
		if($this->getClassAsHtml() != '') $output .= $this->getClassAsHtml();

		// style attribute
		if($this->style != '') $output .= ' style="'. $this->getStyle() .'"';

		// tabindex
		if($this->tabindex !== null) $output .= ' tabindex="'. $this->getTabIndex() .'"';

		// add javascript methods
		if($this->getJavascriptAsHtml() != '') $output .= $this->getJavascriptAsHtml();

		// disabled
		if($this->disabled) $output .= ' disabled="disabled"';

		// readonly
		if($this->readOnly) $output .= ' readonly="readonly"';

		// rows & columns
		$output .= ' cols="'. $this->getCols() .'"';
		$output .= ' rows="'. $this->getRows() .'"';

		// close first tag
		$output .= '>';

		// add value
		$output .= $this->getValue();

		// end tag
		$output .= '</textarea>';

		// template
		if($template !== null)
		{
			$template->assign('txt'. SpoonFilter::toCamelCase($this->name), $output);
			$template->assign('txt'. SpoonFilter::toCamelCase($this->name) .'Error', ($this->errors!= '') ? '<span class="form-error">'. $this->errors .'</span>' : '');
		}

		// cough
		return $output;
	}


	/**
	 * Set the number of columns
	 *
	 * @return	void
	 * @param	int $cols
	 */
	public function setCols($cols)
	{
		$this->cols = (int) $cols;
	}


	/**
	 * This method is not to be used with this class
	 *
	 * @return	void
	 * @param	int $characters
	 */
	public function setMaxlength($characters)
	{
		throw new SpoonFormException('This method is not to be used with the SpoonTextArea class.');
	}


	/**
	 * Set the number of rows
	 *
	 * @return	void
	 * @param	int $rows
	 */
	public function setRows($rows)
	{
		$this->rows = (int) $rows;
	}


	/**
	 * Set the initial value
	 *
	 * @return	void
	 * @param	string $value
	 */
	private function setValue($value)
	{
		$this->value = (string) $value;
	}
}

?>