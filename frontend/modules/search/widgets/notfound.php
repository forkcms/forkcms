<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is a widget with the search form
 *
 * @author Dieter Wyns <dieter.wyns@fork-cms.com>
 */
class FrontendSearchWidgetNotfound extends FrontendBaseWidget
{
	/**
	 * Search results
	 *
	 * @var	array
	 */
	private $results;

	/**
	 * Search term
	 *
	 * @var	string
	 */
	private $term;

	/**
	 * Execute the extra
	 */
	public function execute()
	{
		parent::execute();
		$this->loadTemplate();
		$this->loadData();
		$this->parse();
	}

	/**
	 * Load data
	 */
	private function loadData()
	{
		// Term
		$this->term = $_SERVER['REQUEST_URI'];
	
		// Results
		$this->results = FrontendSearchModel::search($this->term);
	}

	/**
	 * Parse the data into the template
	 */
	private function parse()
	{
		$this->tpl->assign('searchTerm',$this->term);
		$this->tpl->assign('searchResults',$this->results);
	}
}
