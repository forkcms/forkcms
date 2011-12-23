<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This widget will show the latest comments
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendEventsWidgetComments extends BackendBaseWidget
{
	/**
	 * The comments
	 *
	 * @var	array
	 */
	private $comments;

	/**
	 * An array that contains the number of comments / status
	 *
	 * @var int
	 */
	private $numCommentStatus;

	/**
	 * Execute the widget
	 */
	public function execute()
	{
		// set column
		$this->setColumn('middle');

		// load the data
		$this->loadData();

		// parse
		$this->parse();

		// display
		$this->display();
	}

	/**
	 * Load the data
	 */
	private function loadData()
	{
		// get latest published comments
		$this->comments = BackendEventsModel::getLatestComments('published', 5);

		// get count
		$this->numCommentStatus = BackendEventsModel::getCommentStatusCount();
	}

	/**
	 * Parse into template
	 */
	private function parse()
	{
		// assign
		$this->tpl->assign('eventsComments', $this->comments);

		// any comments to moderate?
		if(isset($this->numCommentStatus['moderation']) && (int) $this->numCommentStatus['moderation'] > 0) $this->tpl->assign('eventsNumCommentsToModerate', $this->numCommentStatus['moderation']);
	}
}
