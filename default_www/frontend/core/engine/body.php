<?php

/**
 * Fork
 *
 * This source file is part of Fork CMS.
 *
 * @package		frontend
 * @subpackage	pagebody
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendBody
{
	/**
	 * The content
	 *
	 * @var	string
	 */
	private $content;


	/**
	 * The title
	 *
	 * @var	string
	 */
	private $title;


	/**
	 * Set the content
	 *
	 * @return	void
	 * @param	string $value
	 */
	public function setContent($value)
	{
		$this->content = (string) $value;
	}


	/**
	 * Set the title
	 *
	 * @return	void
	 * @param	string $value
	 */
	public function setTitle($value)
	{
		$this->title = (string) $value;
	}

}
?>