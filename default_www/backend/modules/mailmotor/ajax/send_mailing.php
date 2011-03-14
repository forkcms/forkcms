<?php

/**
 * BackendMailmotorAjaxSendMailing
 *
 * @package		backend
 * @subpackage	mailmotor
 *
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class BackendMailmotorAjaxSendMailing extends BackendBaseAJAXAction
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
		if($mailing['status'] == 'sent') $this->output(900, null, BL::err('MailingAlreadySent', 'mailmotor'));

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
			// fetch campaign ID in CM
			$cmId = BackendMailmotorCMHelper::getCampaignMonitorID('campaign', $id);

			// check if the CM ID isn't false
			if($cmId !== false)
			{
				// delete the mailing in CM
				BackendMailmotorCMHelper::getCM()->deleteCampaign($cmId);

				// delete the reference
				BackendModel::getDB(true)->delete('mailmotor_campaignmonitor_ids', 'cm_id = ?', $cmId);
			}

			// check what error we have
			switch($e->getMessage())
			{
				case 'HTML Content URL Required':
					$message = BL::err('HTMLContentURLRequired', 'mailmotor');
				break;

				case 'Payment details required':
					$message = sprintf(BL::err('PaymentDetailsRequired', 'mailmotor'), BackendModel::getModuleSetting('mailmotor', 'cm_username'));
				break;

				case 'Duplicate Campaign Name':
					$message = BL::err('DuplicateCampaignName', 'mailmotor');
				break;

				default:
					$message = $e->getMessage();
				break;
			}

			// stop the script and show our error
			$this->output(902, null, $message);
		}

		// set status to 'sent'
		$item = array();
		$item['id'] = $id;
		$item['status'] = ($mailing['send_on'] > time()) ? 'queued' : 'sent';

		// update the mailing record
		BackendMailmotorModel::updateMailing($item);

		// we made it \o/
		$this->output(self::OK, array('mailing_id' => $id), BL::msg('MailingSent', 'mailmotor'));
	}
}

?>