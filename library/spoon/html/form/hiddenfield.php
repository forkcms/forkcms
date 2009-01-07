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
require_once 'spoon/html/form/element.php';


/**
 * Creates an html hidden field
 *
 * @package			html
 * @subpackage		form
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @since			1.0.0
 */
class SpoonHiddenField extends SpoonFormElement
{
	/**
	 * Value of this hidden field
	 *
	 * @var	string
	 */
	private $value;


	/**
	 * Class constructor.
	 *
	 * @return	void
	 * @param	string $name
	 * @param	string[optional] $value
	 */
	public function __construct($name, $value = null)
	{
		// obligated fields
		$this->setName($name);

		// value
		if($value !== null) $this->setValue($value);
	}


	/**
	 * Retrieve the initial or submitted value
	 *
	 * @return	string
	 */
	public function getValue()
	{
		// redefine default value
		$value = $this->value;

		// added to form
		if($this->isSubmitted())
		{
			// post/get data
			$data = $this->getMethod(true);

			// submitted by post/get (may be empty)
			if(isset($data[$this->getName()]))
			{
				// value
				$value = (string) $data[$this->getName()];
			}
		}

		return $value;
	}


	/**
	 * Checks if this field has any content (except spaces)
	 *
	 * @return	bool
	 */
	public function isFilled()
	{
		// post/get data
		$data = $this->getMethod(true);

		// validate
		if(!(isset($data[$this->getName()]) && trim($data[$this->getName()]) != '')) return false;
		return true;
	}


	/**
	 * Parses the html for this hidden field
	 *
	 * @return	string
	 * @param	SpoonTemplate[optional] $template
	 */
	public function parse(SpoonTemplate $template = null)
	{
		// name is required
		if($this->name == '') throw new SpoonFormException('A name is required for a hidden field. Please provide a name.');

		// start html generation
		$output = '<input type="hidden"';

		// add id?
		if($this->id !== null) $output .= 'id="'. $this->id .'"';

		// add other elements
		$output .= ' name="'. $this->name .'" value="'. $this->getValue() .'" />';

		// parse hidden field
		if($template !== null) $template->assign('hid'. SpoonFilter::toCamelCase($this->name), $output);

		// cough it up
		return $output;
	}


	/**
	 * Set the value attribute for this hidden field
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