<?php

/**
 * This is the activate-action.
 *
 * @package		frontend
 * @subpackage	profiles
 *
 * @author		Lester Lievens <lester@netlash.com>
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.0
 */
class FrontendProfilesActivate extends FrontendBaseBlock
{
	/**
	 * Execute the extra.
	 *
	 * @return	void
	 */
	public function execute()
	{
		// get activation key
		$key = $this->URL->getParameter(0);

		// load template
		$this->loadTemplate();

		// do we have an activation key?
		if(isset($key))
		{
			// get profile id
			$profileId = FrontendProfilesModel::getIdBySetting('activation_key', $key);

			// have id?
			if($profileId != null)
			{
				// update status
				FrontendProfilesModel::update($profileId, array('status' => 'active'));

				// delete activation key
				FrontendProfilesModel::deleteSetting($profileId, 'activation_key');

				// login profile
				FrontendProfilesAuthentication::login($profileId);

				// show success message
				$this->tpl->assign('activationSuccess', true);
			}

			// failure
			else $this->redirect(FrontendNavigation::getURL(404));
		}

		// missing key
		else $this->redirect(FrontendNavigation::getURL(404));
	}
}

?>
