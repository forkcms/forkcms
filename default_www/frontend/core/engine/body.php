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
	 * Parse the body into the template
	 *
	 * @return	void
	 */
	public function parse()
	{
		// assign title
		if($this->title)
		{
			$this->tpl->assign('oHasBodyTitle', true);
			$this->tpl->assign('bodyTitle', $this->title);
		}
		else $this->tpl->assign('oHasBodyTitle', false);

		// assign content
		if($this->content)
		{
			$this->tpl->assign('oHasBodyContent', true);
			$this->tpl->assign('bodyContent', $this->content);
		}
		else $this->tpl->assign('oHasBodyContent', false);
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