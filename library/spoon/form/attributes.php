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
	 * @param	string $name	The name of the attribute to grab the value for.
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
	 * @param	array $variables	The variables that should be converted into HTML.
	 */
	protected function getAttributesHTML(array $variables)
	{
		// init var
		$html = '';

		// loop attributes
		foreach($this->attributes as $key => $value)
		{
			// class?
			if($key == 'class' && is_callable(array($this, 'getClassHTML')))
			{
				$html .= $this->getClassHTML();
			}

			// other elements
			else
			{
				$html .= ' ' . $key;
				if($value !== null) $html .= '="' . str_replace(array_keys($variables), array_values($variables), $value) . '"';
			}
		}

		return $html;
	}


	/**
	 * Set a custom attribute and its value.
	 *
	 * @return	SpoonFormAttributes
	 * @param	string $key					The name of the attribute.
	 * @param	string[optional] $value		The value for the attribute.
	 */
	public function setAttribute($key, $value = null)
	{
		// redefine
		$key = (string) $key;
		$value = ($value !== null) ? (string) $value : null;

		// key is NOT allowed
		if(in_array(strtolower($key), $this->reservedAttributes))
		{
			throw new SpoonFormException('The key "' . $key . '" is a reserved attribute and can NOT be overwritten.');
		}

		// set attribute
		$this->attributes[strtolower($key)] = $value;
		return $this;
	}


	/**
	 * Set multiple custom attributes at once.
	 *
	 * @return	SpoonFormAttributes
	 * @param	array $attributes	The attributes as key/value-pairs.
	 */
	public function setAttributes(array $attributes)
	{
		foreach($attributes as $key => $value)
		{
			$this->setAttribute($key, $value);
		}

		return $this;
	}
}
