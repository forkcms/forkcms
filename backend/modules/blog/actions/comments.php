<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the comments-action , it will display the overview of blog comments
 *
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendBlogComments extends BackendBaseActionIndex
{
	/**
	 * DataGrids
	 *
	 * @var	BackendDataGridDB
	 */
	private $dgPublished, $dgModeration, $dgSpam;

	/**
	 * Add postdata into the comment
	 *
	 * @param  string $text The comment.
	 * @param string $title The title for the blogarticle.
	 * @param string $URL The URL for the blogarticle.
	 * @param int $id The id of the comment.
	 * @return string
	 */
	public static function addPostData($text, $title, $URL, $id)
	{
		// reset URL
		$URL = BackendModel::getURLForBlock('blog', 'detail') . '/' . $URL . '#comment-' . $id;

		// build HTML
		return '<p><em>' . sprintf(BL::msg('CommentOnWithURL'), $URL, $title) . '</em></p>' . "\n" . (string) $text;
	}

	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		$this->loadDataGrids();
		$this->parse();
		$this->display();
	}

	/**
	 * Loads the datagrids
	 */
	private function loadDataGrids()
	{
		/*
		 * DataGrid for the published comments.
		 */
		$this->dgPublished = new BackendDataGridDB(BackendBlogModel::QRY_DATAGRID_BROWSE_COMMENTS, array('published', BL::getWorkingLanguage(), 'active'));

		// active tab
		$this->dgPublished->setActiveTab('tabPublished');

		// num items per page
		$this->dgPublished->setPagingLimit(30);

		// header labels
		$this->dgPublished->setHeaderLabels(array('created_on' => SpoonFilter::ucfirst(BL::lbl('Date')), 'text' => SpoonFilter::ucfirst(BL::lbl('Comment'))));

		// add the multicheckbox column
		$this->dgPublished->setMassActionCheckboxes('checkbox', '[id]');

		// assign column functions
		$this->dgPublished->setColumnFunction(array('BackendDataGridFunctions', 'getTimeAgo'), '[created_on]', 'created_on', true);
		$this->dgPublished->setColumnFunction(array('BackendDataGridFunctions', 'cleanupPlaintext'), '[text]', 'text', true);
		$this->dgPublished->setColumnFunction(array('BackendBlogComments', 'addPostData'), array('[text]', '[post_title]', '[post_url]', '[id]'), 'text', true);

		// sorting
		$this->dgPublished->setSortingColumns(array('created_on', 'text', 'author'), 'created_on');
		$this->dgPublished->setSortParameter('desc');

		// hide columns
		$this->dgPublished->setColumnsHidden('post_id', 'post_title', 'post_url');

		// add mass action dropdown
		$ddmMassAction = new SpoonFormDropdown('action', array('moderation' => BL::lbl('MoveToModeration'), 'spam' => BL::lbl('MoveToSpam'), 'delete' => BL::lbl('Delete')), 'spam');
		$ddmMassAction->setAttribute('id', 'actionPublished');
		$ddmMassAction->setOptionAttributes('delete', array('data-message-id' => 'confirmDeletePublished'));
		$ddmMassAction->setOptionAttributes('spam', array('data-message-id' => 'confirmSpamPublished'));
		$this->dgPublished->setMassAction($ddmMassAction);

		// check if this action is allowed
		if(BackendAuthentication::isAllowedAction('edit_comment'))
		{
			$this->dgPublished->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit_comment') . '&amp;id=[id]', BL::lbl('Edit'));
		}

		// check if this action is allowed
		if(BackendAuthentication::isAllowedAction('mass_comment_action'))
		{
			$this->dgPublished->addColumn('mark_as_spam', null, BL::lbl('MarkAsSpam'), BackendModel::createURLForAction('mass_comment_action') . '&amp;id=[id]&amp;from=published&amp;action=spam', BL::lbl('MarkAsSpam'));
		}


		/*
		 * DataGrid for the comments that are awaiting moderation.
		 */

		// datagrid for the comments that are awaiting moderation
		$this->dgModeration = new BackendDataGridDB(BackendBlogModel::QRY_DATAGRID_BROWSE_COMMENTS, array('moderation', BL::getWorkingLanguage(), 'active'));

		// active tab
		$this->dgModeration->setActiveTab('tabModeration');

		// num items per page
		$this->dgModeration->setPagingLimit(30);

		// header labels
		$this->dgModeration->setHeaderLabels(array('created_on' => SpoonFilter::ucfirst(BL::lbl('Date')), 'text' => SpoonFilter::ucfirst(BL::lbl('Comment'))));

		// add the multicheckbox column
		$this->dgModeration->setMassActionCheckboxes('checkbox', '[id]');

		// assign column functions
		$this->dgModeration->setColumnFunction(array('BackendDataGridFunctions', 'getTimeAgo'), '[created_on]', 'created_on', true);
		$this->dgModeration->setColumnFunction(array('BackendDataGridFunctions', 'cleanupPlaintext'), '[text]', 'text', true);
		$this->dgModeration->setColumnFunction(array('BackendBlogComments', 'addPostData'), array('[text]', '[post_title]', '[post_url]', '[id]'), 'text', true);

		// sorting
		$this->dgModeration->setSortingColumns(array('created_on', 'text', 'author'), 'created_on');
		$this->dgModeration->setSortParameter('desc');

		// hide columns
		$this->dgModeration->setColumnsHidden('post_id', 'post_title', 'post_url');

		// add mass action dropdown
		$ddmMassAction = new SpoonFormDropdown('action', array('published' => BL::lbl('MoveToPublished'), 'spam' => BL::lbl('MoveToSpam'), 'delete' => BL::lbl('Delete')), 'published');
		$ddmMassAction->setAttribute('id', 'actionModeration');
		$ddmMassAction->setOptionAttributes('delete', array('data-message-id' => 'confirmDeleteModeration'));
		$ddmMassAction->setOptionAttributes('spam', array('data-message-id' => 'confirmSpamModeration'));
		$this->dgModeration->setMassAction($ddmMassAction);

		// check if this action is allowed
		if(BackendAuthentication::isAllowedAction('edit_comment'))
		{
			$this->dgModeration->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit_comment') . '&amp;id=[id]', BL::lbl('Edit'));
		}

		// check if this action is allowed
		if(BackendAuthentication::isAllowedAction('mass_comment_action'))
		{
			$this->dgModeration->addColumn('approve', null, BL::lbl('Approve'), BackendModel::createURLForAction('mass_comment_action') . '&amp;id=[id]&amp;from=published&amp;action=published', BL::lbl('Approve'));
		}


		/*
		 * DataGrid for the comments that are marked as spam
		 */
		$this->dgSpam = new BackendDataGridDB(BackendBlogModel::QRY_DATAGRID_BROWSE_COMMENTS, array('spam', BL::getWorkingLanguage(), 'active'));

		// active tab
		$this->dgSpam->setActiveTab('tabSpam');

		// num items per page
		$this->dgSpam->setPagingLimit(30);

		// header labels
		$this->dgSpam->setHeaderLabels(array('created_on' => SpoonFilter::ucfirst(BL::lbl('Date')), 'text' => SpoonFilter::ucfirst(BL::lbl('Comment'))));

		// add the multicheckbox column
		$this->dgSpam->setMassActionCheckboxes('checkbox', '[id]');

		// assign column functions
		$this->dgSpam->setColumnFunction(array('BackendDataGridFunctions', 'getTimeAgo'), '[created_on]', 'created_on', true);
		$this->dgSpam->setColumnFunction(array('BackendDataGridFunctions', 'cleanupPlaintext'), '[text]', 'text', true);
		$this->dgSpam->setColumnFunction(array('BackendBlogComments', 'addPostData'), array('[text]', '[post_title]', '[post_url]', '[id]'), 'text', true);

		// sorting
		$this->dgSpam->setSortingColumns(array('created_on', 'text', 'author'), 'created_on');
		$this->dgSpam->setSortParameter('desc');

		// hide columns
		$this->dgSpam->setColumnsHidden('post_id', 'post_title', 'post_url');

		// add mass action dropdown
		$ddmMassAction = new SpoonFormDropdown('action', array('published' => BL::lbl('MoveToPublished'), 'moderation' => BL::lbl('MoveToModeration'), 'delete' => BL::lbl('Delete')), 'published');
		$ddmMassAction->setAttribute('id', 'actionSpam');
		$ddmMassAction->setOptionAttributes('delete', array('data-message-id' => 'confirmDeleteSpam'));
		$this->dgSpam->setMassAction($ddmMassAction);

		// check if this action is allowed
		if(BackendAuthentication::isAllowedAction('mass_comment_action'))
		{
			$this->dgSpam->addColumn('approve', null, BL::lbl('Approve'), BackendModel::createURLForAction('mass_comment_action') . '&amp;id=[id]&amp;from=spam&amp;action=published', BL::lbl('Approve'));
		}
	}

	/**
	 * Parse & display the page
	 */
	protected function parse()
	{
		parent::parse();

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
