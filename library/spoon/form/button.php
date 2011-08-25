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
 * Creates a form button.
 *
 * @package		spoon
 * @subpackage	form
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		0.1.1
 */
class SpoonFormButton extends SpoonFormAttributes
{
	/**
	 * Button type (button, reset or submit)
	 *
	 * @var	string
	 */
	private $type = 'submit';


	/**
	 * Html value attribute
	 *
	 * @var	string
	 */
	private $value;


	/**
	 * Class constructor.
	 *
	 * @param	string $name				The name of the button.
	 * @param	string $value				The text that should appear on the button.
	 * @param	string[optional] $type		The type of button.
	 * @param	string[optional] $class		The CSS-class for the button.
	 */
	public function __construct($name, $value, $type = null, $class = 'inputButton')
	{
		// obligated fields
		$this->attributes['id'] = SpoonFilter::toCamelCase($name, '_', true);
		$this->attributes['name'] = (string) $name;
		$this->value = (string) $value;

		// custom optional fields
		if($type !== null) $this->setType($type);
		$this->attributes['class'] = (string) $class;
	}


	/**
	 * Retrieve the initial value.
	 *
	 * @return	string
	 */
	public function getDefaultValue()
	{
		return $this->value;
	}


	/**
	 * Retrieves the button type.
	 *
	 * @return	string
	 */
	public function getType()
	{
		return $this->type;
	}


	/**
	 * Retrieves the value attribute.
	 *
	 * @return	string
	 */
	public function getValue()
	{
		return $this->value;
	}


	/**
	 * Parse the html for this button.
	 *
	 * @return	string
	 * @param	SpoonTemplate[optional] $template	The template to parse the element in.
	 */
	public function parse(SpoonTemplate $template = null)
	{
		// start element
		$output = '<input type="' . $this->type . '" value="' . SpoonFilter::htmlspecialchars($this->value) . '"';

		// add attributes
		$output .= $this->getAttributesHTML(array('[id]' => $this->attributes['id'], '[name]' => $this->attributes['name'], '[value]' => $this->getValue())) . ' />';

		// parse
		if($template !== null) $template->assign('btn' . SpoonFilter::toCamelCase($this->attributes['name']), $output);

		return $output;
	}


	/**
	 * Set the button type (button, reset or submit).
	 *
	 * @return	SpoonFormButton
	 * @param	string[optional] $type		The type of the button.
	 */
	public function setType($type = 'submit')
	{
		$this->type = SpoonFilter::getValue($type, array('button', 'reset', 'submit'), 'submit');
		return $this;
	}
}
