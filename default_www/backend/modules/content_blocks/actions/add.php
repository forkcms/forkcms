<?php

/**
 * BackendContentBlocksAdd
 * This is the add-action, it will display a form to create a new item
 *
 * @package		backend
 * @subpackage	content_blocks
 *
 * @author 		Davy Hellemans <davy@netlash.com>
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendContentBlocksAdd extends BackendBaseActionAdd
{
	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// load the form
		$this->loadForm();

		// validate the form
		$this->validateForm();

		// parse the datagrid
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Load the form
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('add');

		// create elements
		$this->frm->addText('title');
		$this->frm->addEditor('text');
		$this->frm->addCheckbox('hidden', true);
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
			$this->frm->getField('title')->isFilled(BL::getError('TitleIsRequired'));
			$this->frm->getField('text')->isFilled(BL::getError('FieldIsRequired'));

			// no errors?
			if($this->frm->isCorrect())
			{
				// get values
				$values = $this->frm->getValues();

				// insert the item
				$id = BackendContentBlocksModel::insert($values);

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('index') .'&report=added&var='. urlencode($values['title']) .'&highlight=id-'. $id);
			}
		}
	}
}

?>