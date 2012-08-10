<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is a widget that shows all posts in a certain category
 *
 * @author John Poelman <john.poelman@bloobz.be>
 */
class FrontendBlogWidgetPostsPerCategory extends FrontendBaseWidget
{
	/**
	 * The articles
	 *
	 * @var	array
	 */
	private $items;

	/**
	 * Execute the extra
	 */
	public function execute()
	{
		parent::execute();
		$this->loadTemplate();
		$this->getData();
		$this->parse();
	}

	/**
	 * Load the data, don't forget to validate the incoming data
	 */
	private function getData()
	{
		// get the category
		$this->items = FrontendBlogModel::getAllForCategoryID($this->data['id']);
	}

	/**
	 * Parse the data into the template
	 */
	private function parse()
	{
		// assign articles
		$this->tpl->assign('items', $this->items);
	}
}
