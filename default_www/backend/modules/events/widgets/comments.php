<?php

/**
 * This widget will show the latest comments
 *
 * @package		backend
 * @subpackage	events
 *
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @since		2.0
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
	 *
	 * @return	void
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
	 *
	 * @return	void
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
	 *
	 * @return	void
	 */
	private function parse()
	{
		// assign
		$this->tpl->assign('eventsComments', $this->comments);

		// any comments to moderate?
		if(isset($this->numCommentStatus['moderation']) && (int) $this->numCommentStatus['moderation'] > 0) $this->tpl->assign('eventsNumCommentsToModerate', $this->numCommentStatus['moderation']);
	}
}

?>