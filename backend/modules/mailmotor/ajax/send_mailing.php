<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This sends a mailing
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class BackendMailmotorAjaxSendMailing extends BackendBaseAJAXAction
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		// get parameters
		$id = SpoonFilter::getPostValue('id', null, '', 'int');

		// validate
		if($id == '' || !BackendMailmotorModel::existsMailing($id)) $this->output(self::BAD_REQUEST, null, 'No mailing found.');

		// get mailing record
		$mailing = BackendMailmotorModel::getMailing($id);

		/*
			mailing was already sent
			We use a custom status code 900 because we want to do more with JS than triggering an error
		*/
		if($mailing['status'] == 'sent') $this->output(900, null, BL::err('MailingAlreadySent', $this->getModule()));

		// make a regular date out of the send_on timestamp
		$mailing['delivery_date'] = date('Y-m-d H:i:s', $mailing['send_on']);

		// send the mailing
		try
		{
			// only update the mailing if it was queued
			if($mailing['status'] == 'queued') BackendMailmotorCMHelper::updateMailing($mailing);

			// send the mailing if it wasn't queued
			else BackendMailmotorCMHelper::sendMailing($mailing);
		}
		catch(Exception $e)
		{
			// stop the script and show our error
			$this->output(902, null, $e->getMessage());
		}

		// set status to 'sent'
		$item['id'] = $id;
		$item['status'] = ($mailing['send_on'] > time()) ? 'queued' : 'sent';

		// update the mailing record
		BackendMailmotorModel::updateMailing($item);

		// trigger event
		BackendModel::triggerEvent($this->getModule(), 'after_mailing_status_' . $item['status'], array('item' => $item));

		// we made it \o/
		$this->output(self::OK, array('mailing_id' => $item['id']), BL::msg('MailingSent', $this->getModule()));
	}
}
