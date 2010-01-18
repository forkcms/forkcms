<?php

/**
 * BackendBlogComments
 *
 * This is the comments-action , it will display the overview of blog comments
 *
 * @package		backend
 * @subpackage	blog
 *
 * @author 		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class BackendBlogComments extends BackendBaseActionIndex
{
	/**
	 * Datagrids
	 *
	 * @var	BackendDataGridDB
	 */
	private $dgPublished, $dgModeration, $dgSpam;


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// load datagrids
		$this->loadDataGrids();

		// parse page
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Fetch the html for the two buttons
	 *
	 * @return	string
	 * @param	string $type
	 * @param	int $id
	 */
	public static function getCommentActionsHTML($type, $id)
	{
		// published
		if($type == 'published')
		{
			// build html
			$HTML = '<div clas="buttonHolder">
						<a href="'. BackendModel::createURLForAction('mass_comment_action', null, null, array('from' => $type, 'action' => 'moderation', 'id[]' => $id)) .'" class="button"><span><span><span>'. ucfirst(BL::getLabel('MoveToModeration')) .'<span></span></span>
						<a href="'. BackendModel::createURLForAction('mass_comment_action', null, null, array('from' => $type, 'action' => 'spam', 'id[]' => $id)) .'" class="button"><span><span><span>'. ucfirst(BL::getLabel('MoveToSpam')) .'</span></span></span>
					</div>';
		}

		// moderation
		elseif($type == 'moderation')
		{
			// build html
			$HTML = '<div clas="buttonHolder">
						<a href="'. BackendModel::createURLForAction('mass_comment_action', null, null, array('from' => $type, 'action' => 'published', 'id[]' => $id)) .'" class="button"><span><span><span>'. ucfirst(BL::getLabel('MoveToPublished')) .'</span></span></span>
						<a href="'. BackendModel::createURLForAction('mass_comment_action', null, null, array('from' => $type, 'action' => 'spam', 'id[]' => $id)) .'" class="button"><span><span><span>'. ucfirst(BL::getLabel('MoveToSpam')) .'</span></span></span>
					</div>';
		}

		// spam
		else
		{
			// build html
			$HTML = '<div clas="buttonHolder">
						<a href="'. BackendModel::createURLForAction('mass_comment_action', null, null, array('from' => $type, 'action' => 'published', 'id[]' => $id)) .'" class="button"><span><span><span>'. ucfirst(BL::getLabel('MoveToPublished')) .'</span></span></span>
						<a href="'. BackendModel::createURLForAction('mass_comment_action', null, null, array('from' => $type, 'action' => 'moderation', 'id[]' => $id)) .'" class="button"><span><span><span>'. ucfirst(BL::getLabel('MoveToModeration')) .'</span></span></span>
					</div>';
		}

		return $HTML;
	}


	/**
	 * Loads the datagrids
	 *
	 * @return void
	 */
	private function loadDataGrids()
	{
		/*
		 * Datagrid for the published comments.
		 */
		$this->dgPublished = new BackendDataGridDB(BackendBlogModel::QRY_DATAGRID_BROWSE_COMMENTS, 'published');

		// active tab
		$this->dgPublished->setActiveTab('tabPublished');

		// num items per page
		$this->dgPublished->setPagingLimit(5);

		// header labels
		$this->dgPublished->setHeaderLabels(array('created_on' => ucfirst(BL::getLabel('Date')), 'author' => ucfirst(BL::getLabel('Author')), 'text' => ucfirst(BL::getLabel('Comment'))));

		// add the multicheckbox column
		$this->dgPublished->addColumn('checkbox', '<div class="checkboxHolder"><input type="checkbox" name="toggleChecks" value="toggleChecks" />', '<input type="checkbox" name="id[]" value="[id]" class="inputCheckbox" /></div>');
		$this->dgPublished->setColumnsSequence('checkbox');
		$this->dgPublished->addColumn('move');

		// assign column functions
		$this->dgPublished->setColumnFunction('nl2br', '[text]', 'text', true);
		$this->dgPublished->setColumnFunction(array('BackendDataGridFunctions', 'getTimeAgo'), '[created_on]', 'created_on', true);
		$this->dgPublished->setColumnFunction(array('BackendBlogComments', 'getCommentActionsHTML'), array('published', '[id]'), 'move', true);
		$this->dgPublished->setSortingColumns(array('created_on', 'text'), 'text');

		// add mass action dropdown
		$ddmMassAction = new SpoonDropDown('action', array('moderation' => BL::getLabel('MoveToModeration'), 'spam' => BL::getLabel('MoveToSpam'), 'delete' => BL::getLabel('Delete')), 'spam');
		$this->dgPublished->setMassAction($ddmMassAction);

		/*
		 * Datagrid for the comments that are awaiting moderation
		 */
		$this->dgModeration = new BackendDataGridDB(BackendBlogModel::QRY_DATAGRID_BROWSE_COMMENTS, 'moderation');

		// active tab
		$this->dgModeration->setActiveTab('tabModeration');

		// num items per page
		$this->dgModeration->setPagingLimit(5);

		// header labels
		$this->dgModeration->setHeaderLabels(array('created_on' => ucfirst(BL::getLabel('Date')), 'author' => ucfirst(BL::getLabel('Author')), 'text' => ucfirst(BL::getLabel('Comment'))));

		// add the multicheckbox column
		$this->dgModeration->addColumn('checkbox', '<div class="checkboxHolder"><input type="checkbox" name="toggleChecks" value="toggleChecks" />', '<input type="checkbox" name="id[]" value="[id]" class="inputCheckbox" /></div>');
		$this->dgModeration->setColumnsSequence('checkbox');
		$this->dgModeration->addColumn('move');

		// assign column functions
		$this->dgModeration->setColumnFunction('nl2br', '[text]', 'text', true);
		$this->dgModeration->setColumnFunction(array('BackendDataGridFunctions', 'getTimeAgo'), '[created_on]', 'created_on', true);
		$this->dgModeration->setColumnFunction(array('BackendBlogComments', 'getCommentActionsHTML'), array('moderation', '[id]'), 'move', true);
		$this->dgModeration->setSortingColumns(array('created_on', 'text'), 'text');

		// add mass action dropdown
		$ddmMassAction = new SpoonDropDown('action', array('published' => BL::getLabel('MoveToPublished'), 'spam' => BL::getLabel('MoveToSpam'), 'delete' => BL::getLabel('Delete')), 'published');
		$this->dgModeration->setMassAction($ddmMassAction);

		/*
		 * Datagrid for the comments that are marked as spam
		 */
		$this->dgSpam = new BackendDataGridDB(BackendBlogModel::QRY_DATAGRID_BROWSE_COMMENTS, 'spam');

		// active tab
		$this->dgSpam->setActiveTab('tabSpam');

		// num items per page
		$this->dgSpam->setPagingLimit(5);

		// header labels
		$this->dgSpam->setHeaderLabels(array('created_on' => ucfirst(BL::getLabel('Date')), 'author' => ucfirst(BL::getLabel('Author')), 'text' => ucfirst(BL::getLabel('Comment'))));

		// add the multicheckbox column
		$this->dgSpam->addColumn('checkbox', '<div class="checkboxHolder"><input type="checkbox" name="toggleChecks" value="toggleChecks" />', '<input type="checkbox" name="id[]" value="[id]" class="inputCheckbox" /></div>');
		$this->dgSpam->setColumnsSequence('checkbox');
		$this->dgSpam->addColumn('move');

		// assign column functions
		$this->dgSpam->setColumnFunction('nl2br', '[text]', 'text', true);
		$this->dgSpam->setColumnFunction(array('BackendDataGridFunctions', 'getTimeAgo'), '[created_on]', 'created_on', true);
		$this->dgSpam->setColumnFunction(array('BackendBlogComments', 'getCommentActionsHTML'), array('spam', '[id]'), 'move', true);
		$this->dgSpam->setSortingColumns(array('created_on', 'text'), 'text');

		// add mass action dropdown
		$ddmMassAction = new SpoonDropDown('action', array('published' => BL::getLabel('MoveToPublished'), 'moderation' => BL::getLabel('MoveToModeration'), 'delete' => BL::getLabel('Delete')), 'published');
		$this->dgSpam->setMassAction($ddmMassAction);
	}


	/**
	 * Parse & display the page
	 *
	 * @return	void
	 */
	private function parse()
	{
		// published datagrid and num results
		$this->tpl->assign('dgPublished', ($this->dgPublished->getNumResults() != 0) ? $this->dgPublished->getContent() : false);
		$this->tpl->assign('numPublished', $this->dgPublished->getNumResults());

		// moderaton datagrid and num results
		$this->tpl->assign('dgModeration', ($this->dgModeration->getNumResults() != 0) ? $this->dgModeration->getContent() : false);
		$this->tpl->assign('numModeration', $this->dgModeration->getNumResults());

		// spam datagrid and num results
		$this->tpl->assign('dgSpam', ($this->dgSpam->getNumResults() != 0) ? $this->dgSpam->getContent() : false);
		$this->tpl->assign('numSpam', $this->dgSpam->getNumResults());
	}
}

?>