<?php

/**
 * Displays a drag- and dropable overview of all featured articles.
 *
 * @author Jeroen Van den Bossche <jeroen.vandenbossche@wijs.be>
 */
class BackendBlogFeatured extends BackendBaseActionIndex
{
	/**
	 * The datagrid with featured articles.
	 *
	 * @var BackendDataGridDB
	 */
	private $dgFeatured;

	/**
 	 * Execute the action.
	 */
	public function execute()
	{
		parent::execute();
		$this->loadDataGrid();
		$this->parse();
		$this->display();
	}

	/**
	 * Load the featured articles datagrid.
	 */
	protected function loadDataGrid()
	{
		$this->dgFeatured = new BackendDataGridDB(BackendBlogModel::QRY_DATAGRID_BROWSE_FEATURED, array('active', BL::getWorkingLanguage()));

		// make the datagrid drag- and dropable.
		$this->dgFeatured->enableSequenceByDragAndDrop();
		$this->dgFeatured->setAttributes(array('data-action' => 'alter_featured_sequence'));

		$this->dgFeatured->setHeaderLabels(array('user_id' => SpoonFilter::ucfirst(BL::lbl('Author')), 'publish_on' => SpoonFilter::ucfirst(BL::lbl('PublishedOn'))));
		$this->dgFeatured->setColumnFunction(array('BackendDataGridFunctions', 'getLongDate'), array('[publish_on]'), 'publish_on', true);
		$this->dgFeatured->setColumnFunction(array('BackendDataGridFunctions', 'getUser'), array('[user_id]'), 'user_id', true);
		$this->dgFeatured->setRowAttributes(array('id' => 'row-[revision_id]'));

		// if allowed, add direct link to edit action.
		if(BackendAuthentication::isAllowedAction('edit'))
		{
			$this->dgFeatured->setColumnURL('title', BackendModel::createURLForAction('edit') . '&amp;id=[id]');
			$this->dgFeatured->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit') . '&amp;id=[id]', BL::lbl('Edit'));
		}
	}

	/**
	 * Parse the featured articles datagrid.
	 */
	protected function parse()
	{
		$this->tpl->assign('dgFeatured', ($this->dgFeatured->getNumResults() != 0) ? $this->dgFeatured->getContent() : false);
	}
}
