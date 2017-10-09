<?php

namespace Frontend\Modules\Profiles\Actions;

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Profiles\Engine\Authentication as FrontendProfilesAuthentication;

/**
 * This is the index-action, it can be used as a dashboard.
 */
class Index extends FrontendBaseBlock
{
    public function execute(): void
    {
        // only logged in profiles can seer their dashboard
        if (FrontendProfilesAuthentication::isLoggedIn()) {
            // call the parent
            parent::execute();

            /*
             * You could use this as some kind of dashboard where you can show an activity
             * stream, some statistics, ...
             */

            $this->loadTemplate();
        } else {
            // profile not logged in
            $this->redirect(
                FrontendNavigation::getUrlForBlock(
                    'Profiles',
                    'Login'
                ) . '?queryString=' . FrontendNavigation::getUrlForBlock('Profiles'),
                307
            );
        }
    }
}
