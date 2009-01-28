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
 * The base class for every "visual" form element
 *
 * @package			html
 * @subpackage		form
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @since			0.1.1
 */
class SpoonVisualFormElement extends SpoonFormElement
{
	/**
	 * Html class attribute
	 *
	 * @var	string
	 */
	protected $class;


	/**
	 * Html disabled attribute
	 *
	 * @var	bool
	 */
	protected $disabled = false;


	/**
	 * Javascript event functions (eg onblur, onfocus, ..)
	 *
	 * @var	array
	 */
	protected $javascriptEventFunctions = array();


	/**
	 * Is it possible to edit this field
	 *
	 * @var	bool
	 */
	protected $readOnly = false;


	/**
	 * Html style attribute
	 *
	 * @var	string
	 */
	protected $style;


	/**
	 * Html tabindex attribute
	 *
	 * @var	int
	 */
	protected $tabindex;


	/**
	 * Retrieve the class attribute
	 *
	 * @return	string
	 */
	public function getClass()
	{
		return $this->class;
	}


	/**
	 * Retrieve the disabled status
	 *
	 * @return	bool
	 */
	public function getDisabled()
	{
		return $this->disabled;
	}


	/**
	 * Creates html of the javascript functions
	 *
	 * @return	string
	 */
	public function getJavascriptAsHtml()
	{
		// default value
		$value = '';
		
		// loop all elements
		foreach ($this->javascriptEventFunctions as $event => $eventItems)
		{
			// open event tag
			$value .= ' ' . $event . '="';

			// event items have been defined (loop em)
			foreach ($eventItems as $javascript) $value .= $javascript .';';

			// cloase event tag
			$value .= '"';
		}

		// replace id/name tags (easier javascript implementation)
		return str_replace(array('[id]', '[name]'), array($this->getId(), $this->getName()), $value);
	}


	/**
	 * Retrieves all of the event specific javascript functions (or specific ones)
	 *
	 * @return	array
	 * @param	string[optional] $event
	 */
	public function getJavascriptEventFunctions($event = null)
	{
		// event defined
		if($event)
		{
			// redefine event
			$event = (string) $event;

			// item exists
			if(isset($this->javascriptEventFunctions[$event])) return $this->javascriptEventFunctions[$event];

			// item doesn't exist
			return array();
		}

		// all events
		return $this->javascriptEventFunctions;
	}


	/**
	 * Retrieve the read only status
	 *
	 * @return	bool
	 */
	public function getReadOnly()
	{
		return $this->readOnly;
	}


	/**
	 * Retrieve the style attribute
	 *
	 * @return	string
	 */
	public function getStyle()
	{
		return $this->style;
	}


	/**
	 * Retrieve the tabindex attribute
	 *
	 * @return	int
	 */
	public function getTabIndex()
	{
		return $this->tabindex;
	}


	/**
	 * Set the class attribute
	 *
	 * @return	void
	 * @param	string $class
	 */
	public function setClass($class)
	{
		$this->class = (string) $class;
	}


	/**
	 * Set the disabled attribute
	 *
	 * @return	void
	 * @param	bool[optional] $disabled
	 */
	public function setDisabled($disabled = true)
	{
		$this->disabled = (bool) $disabled;
	}


	/**
	 * Set one or more javascript attributes
	 *
	 * @return	void
	 * @param	string $event
	 * @param	string $javascript
	 * @param	bool[optional] $overwrite
	 */
	protected function setJavascriptEventFunction($event, $javascript, $overwrite = false)
	{
		// redefine arguments
		$event = (string) $event;
		$javascript = (string) $javascript;
		$overwrite = (bool) $overwrite;

		// overwrite enabled
		if($overwrite) $this->javascriptEventFunctions[$event] = array($javascript);

		// overwrite disabled
		else $this->javascriptEventFunctions[$event][] = $javascript;
	}


	/**
	 * Set the onblur javascript function(s)
	 *
	 * @return	void
	 * @param	string $javascript
	 * @param	bool[optional] $overwrite
	 */
	public function setOnBlur($javascript, $overwrite = false)
	{
		$this->setJavascriptEventFunction('onblur', $javascript, $overwrite);
	}


	/**
	 * Set the onchange javascript function(s)
	 *
	 * @return	void
	 * @param	string $javascript
	 * @param	bool[optional] $overwrite
	 */
	public function setOnChange($javascript, $overwrite = false)
	{
		$this->setJavascriptEventFunction('onchange', $javascript, $overwrite);
	}


	/**
	 * Set the onclick javascript function(s)
	 *
	 * @return	void
	 * @param	string $javascript
	 * @param	bool[optional] $overwrite
	 */
	public function setOnClick($javascript, $overwrite = false)
	{
		$this->setJavascriptEventFunction('onclick', $javascript, $overwrite);
	}


	/**
	 * Set the ondblclick javascript function(s)
	 *
	 * @return	void
	 * @param	string $javascript
	 * @param	bool[optional] $overwrite
	 */
	public function setOnDoubleClick($javascript, $overwrite = false)
	{
		$this->setJavascriptEventFunction('ondblclick', $javascript, $overwrite);
	}


	/**
	 * Set the onfocus javascript function(s)
	 *
	 * @return	void
	 * @param	string $javascript
	 * @param	bool[optional] $overwrite
	 */
	public function setOnFocus($javascript, $overwrite = false)
	{
		$this->setJavascriptEventFunction('onfocus', $javascript, $overwrite);
	}


	/**
	 * Set the onkeydown javascript function(s)
	 *
	 * @return	void
	 * @param	string $javascript
	 * @param	bool[optional] $overwrite
	 */
	public function setOnKeyDown($javascript, $overwrite = false)
	{
		$this->setJavascriptEventFunction('onkeydown', $javascript, $overwrite);
	}


	/**
	 * Set the onkeypres javascript function(s)
	 *
	 * @return	void
	 * @param	string $javascript
	 * @param	bool[optional] $overwrite
	 */
	public function setOnKeyPress($javascript, $overwrite = false)
	{
		$this->setJavascriptEventFunction('onkeypress', $javascript, $overwrite);
	}


	/**
	 * Set the onkeyup javascript function(s)
	 *
	 * @return	void
	 * @param	string $javascript
	 * @param	bool[optional] $overwrite
	 */
	public function setOnKeyUp($javascript, $overwrite = false)
	{
		$this->setJavascriptEventFunction('onkeyup', $javascript, $overwrite);
	}


	/**
	 * Set the onmousemove javascript function(s)
	 *
	 * @return	void
	 * @param	string $javascript
	 * @param	bool[optional] $overwrite
	 */
	public function setOnMouseMove($javascript, $overwrite = false)
	{
		$this->setJavascriptEventFunction('onmousemove', $javascript, $overwrite);
	}


	/**
	 * Set the onmouseout javascript function(s)
	 *
	 * @return	void
	 * @param	string $javascript
	 * @param	bool[optional] $overwrite
	 */
	public function setOnMouseOut($javascript, $overwrite = false)
	{
		$this->setJavascriptEventFunction('onmouseout', $javascript, $overwrite);
	}


	/**
	 * Set the onmouseover javascript function(s)
	 *
	 * @return	void
	 * @param	string $javascript
	 * @param	bool[optional] $overwrite
	 */
	public function setOnMouseOver($javascript, $overwrite = false)
	{
		$this->setJavascriptEventFunction('onmouseover', $javascript, $overwrite);
	}


	/**
	 * Set the onmousedown javascript function(s)
	 *
	 * @return	void
	 * @param	string $javascript
	 * @param	bool[optional] $overwrite
	 */
	public function setOnMouseDown($javascript, $overwrite = false)
	{
		$this->setJavascriptEventFunction('onmousedown', $javascript, $overwrite);
	}


	/**
	 * Set the onmouseup javascript function(s)
	 *
	 * @return	void
	 * @param	string $javascript
	 * @param	bool[optional] $overwrite
	 */
	public function setOnMouseUp($javascript, $overwrite = false)
	{
		$this->setJavascriptEventFunction('onmouseup', $javascript, $overwrite);
	}


	/**
	 * Set the onselect javascript function(s)
	 *
	 * @return	void
	 * @param	string $javascript
	 * @param	bool[optional] $overwrite
	 */
	public function setOnSelect($javascript, $overwrite = false)
	{
		$this->setJavascriptEventFunction('onselect', $javascript, $overwrite);
	}


	/**
	 * Enable/disable the readonly value
	 *
	 * @return	void
	 * @param	bool[optional] $on
	 */
	public function setReadOnly($on = true)
	{
		$this->readOnly = (bool) $on;
	}


	/**
	 * Set the style attribute
	 *
	 * @return	void
	 * @param	string $style
	 */
	public function setStyle($style)
	{
		$this->style = (string) $style;
	}


	/**
	 * Set the tabindex attribute
	 *
	 * @return	void
	 * @param	string $tabindex
	 */
	public function setTabIndex($tabindex)
	{
		$this->tabindex = (int) $tabindex;
	}
}

?>