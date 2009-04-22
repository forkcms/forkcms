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


/** SpoonVisualElement class */
require_once 'spoon/html/form/visual_element.php';


/**
 * Creates an html form button
 *
 * @package			html
 * @subpackage		form
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @since			0.1.1
 */
class SpoonButton extends SpoonVisualFormElement
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
	 * Class constructor
	 *
	 * @return	void
	 * @param	string $name
	 * @param	string $value
	 * @param	string[optional] $type
	 * @param	string[optional] $class
	 */
	public function __construct($name, $value, $type = null, $class = 'inputButton')
	{
		// obligated fields
		$this->setId(SpoonFilter::toCamelCase($name, '_', true));
		$this->setName($name);
		$this->setValue($value);

		// custom optional fields
		if($type !== null) $this->setType($type);
		if($class !== null) $this->setClass($class);
	}


	/**
	 * Retrieve the initial value
	 *
	 * @return	string
	 */
	public function getDefaultValue()
	{
		return $this->value;
	}


	/**
	 * Retrieves the button type
	 *
	 * @return	string
	 */
	public function getType()
	{
		return $this->type;
	}


	/**
	 * Retrieves the value attribute
	 *
	 * @return	string
	 */
	public function getValue()
	{
		return $this->value;
	}


	/**
	 * Parse the html for this button
	 *
	 * @return	string
	 * @param	SpoonTemplate[optional] $template
	 */
	public function parse(SpoonTemplate $template = null)
	{
		// name required
		if($this->getName() == '') throw new SpoonFormException('A name is required for a button. Please provide a valid name.');

		// value required
		if($this->getValue() == '') throw new SpoonFormException('A value is required for a button. Please provide a value.');

		// start html generation
		$output = '<input type="'. $this->getType() .'" id="'. $this->getId() .'" name="'. $this->getName() .'" value="'. $this->getValue() .'"';

		// class attribute
		if($this->class !== null) $output .= ' class="'. $this->getClass() .'"';

		// style attribute
		if($this->style !== null) $output .= ' style="'. $this->getStyle() .'"';

		// tabindex attribute
		if($this->tabindex !== null) $output .= ' tabindex="'. $this->getTabIndex() .'"';

		// add javascript event functions
		if($this->getJavascriptAsHTML() != '') $output .= $this->getJavascriptAsHTML();

		// disabled attribute
		if($this->disabled) $output .= ' disabled="disabled"';

		// close input tag
		$output .= ' />';

		// parse
		if($template !== null) $template->assign('btn'. SpoonFilter::toCamelCase($this->name), $output);

		// cough it up
		return $output;
	}


	/**
	 * Set the button type (button, reset or submit)
	 *
	 * @return	void
	 * @param	string[optional] $type
	 */
	public function setType($type = 'submit')
	{
		$this->type = SpoonFilter::getValue($type,  array('button', 'reset', 'submit'), 'submit');
	}


	/**
	 * Set the value attribute
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