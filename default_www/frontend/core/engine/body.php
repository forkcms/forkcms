<?php

/**
 * Fork
 *
 * This source file is part of Fork CMS.
 *
 * @package		frontend
 * @subpackage	body
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendBody extends FrontendBaseObject
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
	 * Get the content
	 *
	 * @return	string
	 */
	public function getContent()
	{
		return (string) $this->content;
	}


	/**
	 * Get the title
	 *
	 * @return	string
	 */
	public function getTitle()
	{
		return (string) $this->title;
	}


	/**
	 * Parse the body into the template
	 *
	 * @return	void
	 */
	public function parse()
	{
		// assign title
		$title = $this->getTitle();
		if($title !== null) $this->tpl->assign('bodyTitle', $title);
		$this->tpl->assign('oHasBodyTitle', (bool) ($title !== null));

		// assign content
		$content = $this->getContent();
		if($content !== null) $this->tpl->assign('bodyContent', $content);
		else $this->tpl->assign('oHasBodyContent', (bool) ($content !== null));
	}


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