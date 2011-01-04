<?php

/**
 * Display a form to create a new testimonial.
 *
 * @package		backend
 * @subpackage	testimonials
 *
 * @author		Jan Moesen <jan@netlash.com>
 * @author		Matthias Mullie <matthias@netlash.com>
 * @since		2.1
 */
class BackendTestimonialsAdd extends BackendBaseActionAdd
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

		// set hidden values
		$rbtHiddenValues[] = array('label' => BL::getLabel('Hidden', $this->URL->getModule()), 'value' => 'Y');
		$rbtHiddenValues[] = array('label' => BL::getLabel('Published'), 'value' => 'N');

		// create elements
		$this->frm->addText('name')->setAttribute('id', 'title');
		$this->frm->addEditor('testimonial');
		$this->frm->addRadiobutton('hidden', $rbtHiddenValues, 'N');
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
			$this->frm->getField('name')->isFilled(BackendLanguage::getError('NameIsRequired'));
			$this->frm->getField('testimonial')->isFilled(BackendLanguage::getError('TestimonialIsRequired'));

			// no errors?
			if($this->frm->isCorrect())
			{
				// build item
				$item['name'] = $this->frm->getField('name')->getValue();
				$item['testimonial'] = $this->frm->getField('testimonial')->getValue();
				$item['hidden'] = $this->frm->getField('hidden')->getValue();
				$item['user_id'] = BackendAuthentication::getUser()->getUserId();
				$item['language'] = BackendLanguage::getWorkingLanguage();
				$item['sequence'] = BackendTestimonialsModel::getMaximumSequence() + 1;
				$item['created_on'] = BackendModel::getUTCDate();
				$item['edited_on'] = BackendModel::getUTCDate();

				// insert the item
				$item['id'] = BackendTestimonialsModel::insert($item);

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('index') .'&report=added&var='. urlencode($item['name']) .'&highlight=row-'. $item['id']);
			}
		}
	}
}

?>