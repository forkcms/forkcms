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


/** Spoon class */
require_once 'spoon/spoon.php';

/** SpoonFilter clas */
require_once 'spoon/filter/filter.php';


/**
 * The base class for every form element
 *
 * @package			html
 * @subpackage		form
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @since			0.1.1
 */
class SpoonFormElement
{
	/**
	 * Name of the form this element is a part of
	 *
	 * @var	string
	 */
	protected $formName;


	/**
	 * Html id attribute
	 *
	 * @var	string
	 */
	protected $id;


	/**
	 * Method inherited from the form (post/get)
	 *
	 * @var	string
	 */
	protected $method = 'post';


	/**
	 * Html name attribute
	 *
	 * @var	string
	 */
	protected $name;


	/**
	 * Retrieves the id attribute
	 *
	 * @return	string
	 */
	public function getId()
	{
		return $this->id;
	}


	/**
	 * Retrieve the form method or the submitted data
	 *
	 * @return	string
	 * @param	bool[optional] $array
	 */
	public function getMethod($array = false)
	{
		if($array) return ($this->method == 'post') ? $_POST : $_GET;
		return $this->method;
	}


	/**
	 * Retrieves the name attribute
	 *
	 * @return	string
	 */
	public function getName()
	{
		return $this->name;
	}


	/**
	 * Returns whether this form has been submitted
	 *
	 * @return	bool
	 */
	public function isSubmitted()
	{
		// post/get data
		$data = $this->getMethod(true);

		// name given
		if($this->formName != null && isset($data['form']) && $data['form'] == $this->formName) return true;

		// no name given
		elseif($this->formName == null && $_SERVER['REQUEST_METHOD'] == strtoupper($this->method)) return true;

		// everything else
		else return false;
	}


	/**
	 * Parse the html for the current element
	 *
	 * @return	void
	 * @param	SpoonTemplate $template
	 */
	public function parse(SpoonTemplate $template = null)
	{
		// filled by subclasses
	}


	/**
	 * Set the name of the form this field is a part of
	 *
	 * @return	void
	 * @param	string $name
	 */
	public function setFormName($name)
	{
		$this->formName = (string) $name;
	}


	/**
	 * Set the id attribute
	 *
	 * @return	void
	 * @param	string $id
	 */
	public function setId($id)
	{
		$this->id = (string) $id;
	}


	/**
	 * Set the form method
	 *
	 * @return	void
	 * @param	string[optional] $method
	 */
	public function setMethod($method = 'post')
	{
		$this->method = SpoonFilter::getValue($method, array('get', 'post'), 'post');
	}


	/**
	 * Set the name attribute
	 *
	 * @return	void
	 * @param	string $name
	 */
	public function setName($name)
	{
		$this->name = (string) $name;
	}
}

?>