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
 * Creates hidden form element.
 *
 * @package		spoon
 * @subpackage	form
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		1.0.0
 */
class SpoonFormHidden extends SpoonFormAttributes
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
	 * @param	string $name					The name.
	 * @param	string[optional] $value			The initial value.
	 */
	public function __construct($name, $value = null)
	{
		// obligated fields
		$this->attributes['id'] = SpoonFilter::toCamelCase((string) $name, '_', true);
		$this->attributes['name'] = (string) $name;

		// value
		if($value !== null) $this->value = (string) $value;
	}


	/**
	 * Retrieve the initial or submitted value.
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
			if(isset($data[$this->attributes['name']]))
			{
				// value
				$value = (string) $data[$this->attributes['name']];
			}
		}

		return $value;
	}


	/**
	 * Checks if this field has any content (except spaces).
	 *
	 * @return	bool
	 */
	public function isFilled()
	{
		// post/get data
		$data = $this->getMethod(true);

		// validate
		if(!(isset($data[$this->attributes['name']]) && trim((string) $data[$this->attributes['name']]) != '')) return false;
		return true;
	}


	/**
	 * Parses the html for this hidden field.
	 *
	 * @return	string
	 * @param	SpoonTemplate[optional] $template	The template to parse the element in.
	 */
	public function parse(SpoonTemplate $template = null)
	{
		// start html generation
		$output = '<input type="hidden" value="' . $this->getValue() . '"';

		// build attributes
		$attributes = array();
		if(isset($this->attributes['id'])) $attributes['[id]'] = $this->attributes['id'];
		$attributes['[name]'] = $this->attributes['name'];
		$attributes['[value]'] = $this->getValue();

		// add attributes
		$output .= $this->getAttributesHTML($attributes) . ' />';

		// parse hidden field
		if($template !== null) $template->assign('hid' . SpoonFilter::toCamelCase($this->attributes['name']), $output);

		return $output;
	}
}
