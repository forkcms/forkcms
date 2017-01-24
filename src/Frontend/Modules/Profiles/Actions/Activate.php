<?php

namespace Frontend\Modules\Profiles\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Profiles\Engine\Authentication as FrontendProfilesAuthentication;
use Frontend\Modules\Profiles\Engine\Model as FrontendProfilesModel;

/**
 * This is the activate-action.
 */
class Activate extends FrontendBaseBlock
{
    /**
     * Execute the extra.
     */
    public function execute()
    {
        // get activation key
        $key = $this->URL->getParameter(0);

        // load template
        $this->loadTemplate();

        // do we have an activation key?
        if (isset($key)) {
            // get profile id
            $profileId = FrontendProfilesModel::getIdBySetting('activation_key', $key);

            // have id?
            if ($profileId != null) {
                // update status
                FrontendProfilesModel::update($profileId, array('status' => 'active'));

                // delete activation key
                FrontendProfilesModel::deleteSetting($profileId, 'activation_key');

                // login profile
                FrontendProfilesAuthentication::login($profileId);

                // show success message
                $this->tpl->assign('activationSuccess', true);
            } else {
                // failure
                $this->redirect(FrontendNavigation::getURL(404));
            }
        } else {
            $this->redirect(FrontendNavigation::getURL(404));
        }
    }
}
