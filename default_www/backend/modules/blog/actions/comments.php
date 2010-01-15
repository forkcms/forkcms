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
						<a href="'. BackendModel::createURLForAction('comment_status', null, null, array('from' => $type, 'to' => 'moderation', 'id[]' => $id)) .'" class="button"><span><span><span>'. ucfirst(BL::getLabel('MoveToModeration')) .'<span></span></span>
						<a href="'. BackendModel::createURLForAction('comment_status', null, null, array('from' => $type, 'to' => 'spam', 'id[]' => $id)) .'" class="button"><span><span><span>'. ucfirst(BL::getLabel('MoveToSpam')) .'</span></span></span>
					</div>';
		}

		// moderation
		elseif($type == 'moderation')
		{
			// build html
			$HTML = '<div clas="buttonHolder">
						<a href="'. BackendModel::createURLForAction('comment_status', null, null, array('from' => $type, 'to' => 'published', 'id[]' => $id)) .'" class="button"><span><span><span>'. ucfirst(BL::getLabel('MoveToPublished')) .'</span></span></span>
						<a href="'. BackendModel::createURLForAction('comment_status', null, null, array('from' => $type, 'to' => 'spam', 'id[]' => $id)) .'" class="button"><span><span><span>'. ucfirst(BL::getLabel('MoveToSpam')) .'</span></span></span>
					</div>';
		}

		// spam
		else
		{
			// build html
			$HTML = '<div clas="buttonHolder">
						<a href="'. BackendModel::createURLForAction('comment_status', null, null, array('from' => $type, 'to' => 'published', 'id[]' => $id)) .'" class="button"><span><span><span>'. ucfirst(BL::getLabel('MoveToPublished')) .'</span></span></span>
						<a href="'. BackendModel::createURLForAction('comment_status', null, null, array('from' => $type, 'to' => 'moderation', 'id[]' => $id)) .'" class="button"><span><span><span>'. ucfirst(BL::getLabel('MoveToModeration')) .'</span></span></span>
					</div>';
		}

		return $HTML;
	}

	private function loadDataGrids()
	{
		// published comments
		$this->dgPublished = new BackendDataGridDB(BackendBlogModel::QRY_DATAGRID_BROWSE_COMMENTS, 'published');
		$this->dgPublished->setHeaderLabels(array('created_on' => ucfirst(BL::getLabel('Date')), 'author' => ucfirst(BL::getLabel('Author')), 'text' => ucfirst(BL::getLabel('Comment'))));
		$this->dgPublished->setColumnFunction('nl2br', '[text]', 'text', true); // @todo write nl2p
		$this->dgPublished->setColumnFunction(array('BackendDataGridFunctions', 'getTimeAgo'), '[created_on]', 'created_on', true);
		$this->dgPublished->setActiveTab('tabPublished');
		$this->dgPublished->setPagingLimit(5);
		$this->dgPublished->addColumn('checkbox', '<div class="checkboxHolder"><input type="checkbox" name="boingboing" value="woohoo" />', '<input type="checkbox" name="id[]" value="[id]" class="inputCheckbox" /></div>');
		$this->dgPublished->setColumnsSequence('checkbox');
		$this->dgPublished->setColumnAttributes('checkbox', array('class' => 'checkboxHolder', 'width' => '2%'));
		$this->dgPublished->addColumn('move');
		$this->dgPublished->setColumnFunction(array('BackendBlogComments', 'getCommentActionsHTML'), array('published', '[id]'), 'move', true);
		$this->dgPublished->setSortingColumns(array('created_on', 'text'), 'text');
		$this->dgPublished->setColumnHeaderAttributes('checkbox', array('width' => '2%', 'class' => 'checkboxHolder'));

		$ddmMassAction = new SpoonDropDown('to', array('moderation' => ucfirst(BL::getLabel('MoveToModeration')), 'spam' => ucfirst(BL::getLabel('MoveToSpam'))), 'spam');
		$this->dgPublished->setMassAction($ddmMassAction);

		// assign datagrid & num results
		$this->tpl->assign('dgPublished', ($this->dgPublished->getNumResults() != 0) ? $this->dgPublished->getContent() : false);
		$this->tpl->assign('numPublished', $this->dgPublished->getNumResults());

		$this->tpl->assign('numModeration', 0);
		$this->tpl->assign('numSpam', 0);
	}
}

?>