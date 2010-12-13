<?php

/**
 * BackendTagsEdit
 * This is the edit action, it will display a form to edit an existing tag.
 *
 * @package		backend
 * @subpackage	tags
 *
 * @author 		Dave Lens <dave@netlash.com>
 * @author		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class BackendTagsEdit extends BackendBaseActionEdit
{
	/**
	 * Datagrid with the articles linked to the current tag
	 *
	 * @var	BackendDataGridArray
	 */
	protected $dgUsage;


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// get parameters
		$this->id = $this->getParameter('id', 'int');

		// does the item exist
		if($this->id !== null && BackendTagsModel::exists($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// get all data for the item we want to edit
			$this->getData();

			// load the datagrid
			$this->loadDatagrid();

			// load the form
			$this->loadForm();

			// validate the form
			$this->validateForm();

			// parse the page
			$this->parse();

			// display the page
			$this->display();
		}

		// no item found, throw an exceptions, because somebody is fucking with our URL
		else $this->redirect(BackendModel::createURLForAction('index') .'&error=non-existing');
	}


	/**
	 * Get the data
	 *
	 * @return	void
	 */
	private function getData()
	{
		$this->record = BackendTagsModel::get($this->id);
	}


	/**
	 * Load the datagrid
	 *
	 * @return	void
	 */
	private function loadDatagrid()
	{
		// init var
		$items = array();

		// get active modules
		$modules = BackendModel::getModules();

		// loop active modules
		foreach($modules as $module)
		{
			// check if their is a model-file
			if(SpoonFile::exists(BACKEND_MODULES_PATH .'/'. $module .'/engine/model.php'))
			{
				// require the model-file
				require_once BACKEND_MODULES_PATH .'/'. $module .'/engine/model.php';

				// build class name
				$className = SpoonFilter::toCamelCase('backend_'. $module .'_model');

				// check if the getByTag-method is available
				if(method_exists($className, 'getByTag'))
				{
					// make the call and get the item
					$moduleItems = (array) call_user_func(array($className, 'getByTag'), $this->id);

					// loop items
					foreach($moduleItems as $row)
					{
						// check if needed fields are available
						if(isset($row['url'], $row['name'], $row['module']))
						{
							// add
							$items[] = array('module' => ucfirst(BL::getLabel(SpoonFilter::toCamelCase($row['module']))), 'name' => $row['name'], 'url' => $row['url']);
						}
					}
				}
			}
		}

		// create datagrid
		$this->dgUsage = new BackendDataGridArray($items);

		// disable paging
		$this->dgUsage->setPaging(false);

		// hide columns
		$this->dgUsage->setColumnsHidden(array('url'));

		// set headers
		$this->dgUsage->setHeaderLabels(array('name' => ucfirst(BL::getLabel('Title')), 'url' => ''));

		// set url
		$this->dgUsage->setColumnURL('name', '[url]', ucfirst(BL::getLabel('Edit')));

		// add use column
		$this->dgUsage->addColumn('edit', null, ucfirst(BL::getLabel('Edit')), '[url]', BL::getLabel('Edit'));
	}


	/**
	 * Load the form
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('edit');

		// create elements
		$this->frm->addText('name', $this->record['name']);
	}


	/**
	 * Parse the form
	 *
	 * @return	void
	 */
	protected function parse()
	{
		// call parent
		parent::parse();

		// assign id, name
		$this->tpl->assign('id', $this->id);
		$this->tpl->assign('name', $this->record['name']);

		// assign usage-datagrid
		$this->tpl->assign('usage', ($this->dgUsage->getNumResults() != 0) ? $this->dgUsage->getContent() : false);
	}


	/**
	 * Validate the form
	 *
	 * @return	void
	 */
	private function validateForm()
	{
		// is the form submitted?
		if($this->frm->isSubmitted())
		{
			// cleanup the submitted fields, ignore fields that were added by hackers
			$this->frm->cleanupFields();

			// validate fields
			$this->frm->getField('name')->isFilled(BL::getError('NameIsRequired'));

			// no errors?
			if($this->frm->isCorrect())
			{
				// build tag
				$tag = array();
				$tag['id'] = $this->id;
				$tag['tag'] = $this->frm->getField('name')->getValue();
				$tag['url'] = BackendTagsModel::getURL($tag['tag'], $this->id);

				// upate the item
				BackendTagsModel::updateTag($tag);

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('index') .'&report=edited&var='. urlencode($tag['tag']) .'&highlight=row-'. $this->id);
			}
		}
	}
}

?>