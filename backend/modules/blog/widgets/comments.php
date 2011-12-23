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
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 */
class BackendBlogWidgetComments extends BackendBaseWidget
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
		$this->setColumn('middle');
		$this->setPosition(0);
		$this->loadData();
		$this->parse();
		$this->display();
	}

	/**
	 * Load the data
	 */
	private function loadData()
	{
		$this->comments = BackendBlogModel::getLatestComments('published', 5);
		$this->numCommentStatus = BackendBlogModel::getCommentStatusCount();
	}

	/**
	 * Parse into template
	 */
	private function parse()
	{
		$this->tpl->assign('blogComments', $this->comments);

		// comments to moderate
		if(isset($this->numCommentStatus['moderation']) && (int) $this->numCommentStatus['moderation'] > 0)
		{
			$this->tpl->assign('blogNumCommentsToModerate', $this->numCommentStatus['moderation']);
		}
	}
}
