<?php

/**
 * This is the import action, it will display a form to import a XML locale file.
 *
 * @package		backend
 * @subpackage	locale
 *
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.0
 */
class BackendLocaleImport extends BackendBaseActionAdd
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
		$this->frm = new BackendForm('import');

		// create and add elements
		$this->frm->addFile('file');
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

			// redefine fields
			$fileFile = $this->frm->getField('file');

			// name checks
			if($fileFile->isFilled(BL::err('FieldIsRequired')))
			{
				$fileFile->isAllowedExtension(array('xml'), sprintf(BL::getError('ExtensionNotAllowed'), 'xml'));
			}

			// no errors?
			if($this->frm->isCorrect())
			{

				// everything is saved, so redirect to the overview
//				$this->redirect(BackendModel::createURLForAction('index', null, null, array('language' => $this->filter['language'], 'application' => $this->filter['application'], 'module' => $this->filter['module'], 'type' => $this->filter['type'], 'name' => $this->filter['name'], 'value' => $this->filter['value'])) .'&report=added&var='. urlencode($item['name']) .'&highlight=row-'. $item['id']);
			}
		}
	}
}

?>