<?php

namespace Frontend\Modules\Profiles\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Profiles\Engine\Authentication as FrontendProfilesAuthentication;

/**
 * This is the index-action, it can be used as a dashboard.
 *
 * @author Lester Lievens <lester@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class Index extends FrontendBaseBlock
{
    /**
     * Execute the extra.
     */
    public function execute()
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
                FrontendNavigation::getURLForBlock(
                    'Profiles',
                    'Login'
                ) . '?queryString=' . FrontendNavigation::getURLForBlock('Profiles'),
                307
            );
        }
    }
}
