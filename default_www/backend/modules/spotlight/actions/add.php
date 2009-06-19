<?php

/**
 * SpotlightAdd
 *
 * This is the add-action, it will display a form to create a new spotlight item
 *
 * @package		backend
 * @subpackage	spotlight
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class SpotlightAdd extends BackendBaseActionAdd
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
		$this->frm->addTextField('title');
		$this->frm->addEditorField('content');
		$this->frm->addCheckBox('hidden', true);
		$this->frm->addButton('submit', ucfirst(BL::getLabel('Add')), 'submit');
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
			$this->frm->getField('content')->isFilled(BL::getError('ContentIsRequired'));

			// no errors?
			if($this->frm->getCorrect())
			{
				// get values
				$values = (array) $this->frm->getValues();

				// insert the item
				$id = (int) BackendSpotlightModel::insert($values);

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('index') .'?report=added&var='. urlencode($values['title']) .'&hilight=id-'. $id);
			}
		}
	}
}

?>