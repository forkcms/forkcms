<?php

/**
 * Display a form to edit an existing testimonial.
 *
 * @package		backend
 * @subpackage	testimonials
 *
 * @author		Jan Moesen <jan@netlash.com>
 * @author		Matthias Mullie <matthias@netlash.com>
 * @since		2.1
 */
class BackendTestimonialsEdit extends BackendBaseActionEdit
{
	/**
	 * Execute the action.
	 *
	 * @return	void
	 */
	public function execute()
	{
		// get parameters
		$this->id = $this->getParameter('id', 'int');

		// does the testimonial exist
		if($this->id !== null && BackendTestimonialsModel::exists($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// get data
			$this->getData();

			// load form
			$this->loadForm();

			// validate form
			$this->validateForm();

			// parse
			$this->parse();

			// display
			$this->display();
		}

		// no testimonial found
		else $this->redirect(BackendModel::createURLForAction('index') .'&error=non-existing');
	}


	/**
	 * Get the data.
	 *
	 * @return	void
	 */
	private function getData()
	{
		$this->record = BackendTestimonialsModel::get($this->id);
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

		// set hidden values
		$rbtHiddenValues[] = array('label' => BL::getLabel('Hidden'), 'value' => 'Y');
		$rbtHiddenValues[] = array('label' => BL::getLabel('Published'), 'value' => 'N');

		// create elements
		$this->frm->addText('name', $this->record['name'])->setAttribute('id', 'title');
		$this->frm->addEditor('testimonial', $this->record['testimonial']);
		$this->frm->addRadiobutton('hidden', $rbtHiddenValues, $this->record['hidden']);
	}


	/**
	 * Parse the form.
	 *
	 * @return	void
	 */
	protected function parse()
	{
		// call parent
		parent::parse();

		// assign fields
		$this->tpl->assign('item', $this->record);
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
				$item['id'] = $this->id;
				$item['name'] = $this->frm->getField('name')->getValue();
				$item['testimonial'] = $this->frm->getField('testimonial')->getValue();
				$item['hidden'] = $this->frm->getField('hidden')->getValue();
				$item['user_id'] = BackendAuthentication::getUser()->getUserId();
				$item['edited_on'] = BackendModel::getUTCDate();

				// update the testimonial
				BackendTestimonialsModel::update($item);

				// everything has been saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('index') .'&report=edited&var='. urlencode($item['name']) .'&highlight=row-'. $item['id']);
			}
		}
	}
}

?>