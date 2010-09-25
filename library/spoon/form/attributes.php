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
 * The base class for every form element that wants to implement the standard
 * way for dealing with attributes.
 *
 * @package		spoon
 * @subpackage	form
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		0.1.1
 */
class SpoonFormAttributes extends SpoonFormElement
{
	/**
	 * Retrieve the value for a specific attribute.
	 *
	 * @return	string
	 * @param	string $name
	 */
	public function getAttribute($name)
	{
		return (isset($this->attributes[(string) $name])) ? $this->attributes[(string) $name] : null;
	}


	/**
	 * Retrieves the custom attributes.
	 *
	 * @return	array
	 */
	public function getAttributes()
	{
		return $this->attributes;
	}


	/**
	 * Retrieves the custom attributes as HTML.
	 *
	 * @return	string
	 * @param	array $variables
	 */
	protected function getAttributesHTML(array $variables)
	{
		// init var
		$html = '';

		// loop attributes
		foreach($this->attributes as $key => $value)
		{
			// class?
			if($key == 'class' && method_exists($this, 'getClassHTML'))
			{
				$html .= $this->getClassHTML();
			}

			// other elements
			else
			{
				$html .= ' '. $key;
				if($value !== null) $html .= '="'. str_replace(array_keys($variables), array_values($variables), $value) .'"';
			}
		}

		return $html;
	}


	/**
	 * Set a custom attribute and its value.
	 *
	 * @return	void
	 * @param	string $key
	 * @param	string $value
	 */
	public function setAttribute($key, $value = null)
	{
		// redefine
		$key = (string) $key;
		$value = ($value !== null) ? (string) $value : null;

		// key is NOT allowed
		if(in_array(strtolower($key), $this->reservedAttributes)) throw new SpoonFormException('The key "'. $key .'" is a reserved attribute and can NOT be overwritten.');

		// set attribute
		$this->attributes[strtolower($key)] = $value;
	}


	/**
	 * Set multiple custom attributes at once.
	 *
	 * @return	void
	 * @param	array $attributes
	 */
	public function setAttributes(array $attributes)
	{
		foreach($attributes as $key => $value) $this->setAttribute($key, $value);
	}
}

?>