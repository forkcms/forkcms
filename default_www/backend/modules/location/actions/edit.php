<?php

/**
 * This is the edit-action, it will display a form to create a new item
 *
 * @package		backend
 * @subpackage	location
 *
 * @author		Matthias Mullie <matthias@netlash.com>
 * @since		2.1
 */
class BackendLocationEdit extends BackendBaseActionEdit
{
	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// get parameters
		$this->id = $this->getParameter('id', 'int');

		// does the item exists
		if($this->id !== null && BackendLocationModel::exists($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// get all data for the item we want to edit
			$this->getData();

			// load the form
			$this->loadForm();

			// validate the form
			$this->validateForm();

			// parse
			$this->parse();

			// display the page
			$this->display();
		}

		// no item found, throw an exception, because somebody is fucking with our URL
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}


	/**
	 * Get the data
	 *
	 * @return	void
	 */
	private function getData()
	{
		// get the record
		$this->record = (array) BackendLocationModel::get($this->id);

		// no item found, throw an exceptions, because somebody is fucking with our URL
		if(empty($this->record)) $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
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
		$this->frm->addText('title', $this->record['title']);
		$this->frm->addEditor('text', $this->record['text']);
		$this->frm->addText('street', $this->record['street']);
		$this->frm->addText('number', $this->record['number']);
		$this->frm->addText('zip', $this->record['zip']);
		$this->frm->addText('city', $this->record['city']);
		$this->frm->addDropdown('country', SpoonLocale::getCountries(BL::getInterfaceLanguage()), $this->record['country']);
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

		// get settings
		$settings = BackendModel::getModuleSettings();

		// assign to template
		$this->tpl->assign('item', $this->record);
		$this->tpl->assign('settings', $settings['location']);

		// assign message if address was not be geocoded
		if($this->record['lat'] == null || $this->record['lng'] == null) $this->tpl->assign('errorMessage', BL::err('AddressCouldNotBeGeocoded'));
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
			$this->frm->getField('title')->isFilled(BL::err('TitleIsRequired'));
			$this->frm->getField('street')->isFilled(BL::err('FieldIsRequired'));
			$this->frm->getField('number')->isFilled(BL::err('FieldIsRequired'));
			$this->frm->getField('zip')->isFilled(BL::err('FieldIsRequired'));
			$this->frm->getField('city')->isFilled(BL::err('FieldIsRequired'));

			// no errors?
			if($this->frm->isCorrect())
			{
				// build item
				$item['id'] = $this->id;
				$item['language'] = BL::getWorkingLanguage();
				$item['extra_id'] = $this->record['extra_id'];
				$item['title'] = $this->frm->getField('title')->getValue();
				$item['text'] = $this->frm->getField('text')->getValue();
				$item['street'] = $this->frm->getField('street')->getValue();
				$item['number'] = $this->frm->getField('number')->getValue();
				$item['zip'] = $this->frm->getField('zip')->getValue();
				$item['city'] = $this->frm->getField('city')->getValue();
				$item['country'] = $this->frm->getField('country')->getValue();

				// check if it's neccessary to geocode again
				if($this->record['lat'] === null || $this->record['lng'] === null || $item['street'] != $this->record['street'] || $item['number'] != $this->record['number'] || $item['zip'] != $this->record['zip'] || $item['city'] != $this->record['city'] || $item['country'] != $this->record['country'])
				{
					// geocode address
					$url = 'http://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($item['street'] . ' ' . $item['number'] . ', ' . $item['zip'] . ' ' . $item['city'] . ', ' . SpoonLocale::getCountry($item['country'], BL::getWorkingLanguage())) . '&sensor=false';
					$geocode = json_decode(SpoonHTTP::getContent($url));
					$item['lat'] = isset($geocode->results[0]->geometry->location->lat) ? $geocode->results[0]->geometry->location->lat : null;
					$item['lng'] = isset($geocode->results[0]->geometry->location->lng) ? $geocode->results[0]->geometry->location->lng : null;
				}

				// old values are still good
				else
				{
					$item['lat'] = $this->record['lat'];
					$item['lng'] = $this->record['lng'];
				}


				// insert the item
				$id = BackendLocationModel::update($item);

				// edit search index
//				if(is_callable(array('BackendSearchModel', 'editIndex'))) BackendSearchModel::editIndex('location', (int) $id, array('title' => $item['title'], 'text' => $item['text']));

				// everything is saved, so redirect to the overview
				if($item['lat'] && $item['lng']) $this->redirect(BackendModel::createURLForAction('index') . '&report=edited&var=' . urlencode($item['title']) . '&highlight=row-' . $id);

				// could not geocode, redirect to edit
				else $this->redirect(BackendModel::createURLForAction('edit') . '&id=' . $item['id']);
			}
		}
	}
}

?>