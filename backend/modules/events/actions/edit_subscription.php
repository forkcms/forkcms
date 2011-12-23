<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the edit-action, it will display a form to edit an existing item
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendEventsEditSubscription extends BackendBaseActionEdit
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		// get parameters
		$this->id = $this->getParameter('id', 'int');

		// does the item exists
		if($this->id !== null && BackendEventsModel::existsSubscription($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// get all data for the item we want to edit
			$this->getData();

			// load the form
			$this->loadForm();

			// validate the form
			$this->validateForm();

			// parse the datagrid
			$this->parse();

			// display the page
			$this->display();
		}

		// no item found, throw an exception, because somebody is fucking with our URL
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}

	/**
	 * Get the data
	 * If a revision-id was specified in the URL we load the revision and not the actual data.
	 */
	private function getData()
	{
		// get the record
		$this->record = (array) BackendEventsModel::getSubscription($this->id);

		// no item found, throw an exceptions, because somebody is fucking with our URL
		if(empty($this->record)) $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}

	/**
	 * Load the form
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('editSubscription');

		// create elements
		$this->frm->addText('author', $this->record['author']);
		$this->frm->addText('email', $this->record['email']);

		// assign URL
		$this->tpl->assign('itemURL', BackendModel::getURLForBlock('events', 'detail') . '/' . $this->record['event_url'] . '#subscription-' . $this->record['event_id']);
		$this->tpl->assign('itemTitle', $this->record['event_title']);
	}

	/**
	 * Validate the form
	 */
	private function validateForm()
	{
		// is the form submitted?
		if($this->frm->isSubmitted())
		{
			// cleanup the submitted fields, ignore fields that were added by hackers
			$this->frm->cleanupFields();

			// validate fields
			$this->frm->getField('author')->isFilled(BL::err('AuthorIsRequired'));
			$this->frm->getField('email')->isEmail(BL::err('EmailIsInvalid'));

			// no errors?
			if($this->frm->isCorrect())
			{
				// build item
				$item['id'] = $this->id;
				$item['status'] = $this->record['status'];
				$item['author'] = $this->frm->getField('author')->getValue();
				$item['email'] = $this->frm->getField('email')->getValue();

				// insert the item
				BackendEventsModel::updateSubscription($item);

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('subscriptions') . '&report=edited-subscription&id=' . $item['id'] . '&highlight=row-' . $item['id'] . '#tab' . SpoonFilter::toCamelCase($item['status']));
			}
		}
	}
}
