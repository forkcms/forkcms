<?php

namespace Frontend\Modules\Profiles\Widgets;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Common\Exception\RedirectException;
use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Core\Engine\Navigation;
use Frontend\Modules\Profiles\Engine\Authentication as FrontendProfilesAuthentication;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * This is a widget to help you secure a page and make it only accessible for logged-in users.
 */
class SecurePage extends FrontendBaseWidget
{
    /**
     * Execute the extra
     */
    public function execute()
    {
        parent::execute();
        $this->loadTemplate();

        // Check if we're logged in, else redirect to the login form.
        if (!FrontendProfilesAuthentication::isLoggedIn()) {
            $queryString = $this->URL->getQueryString();
            throw new RedirectException(
                'Redirect',
                new RedirectResponse(Navigation::getURLForBlock('Profiles', 'Login') . '?queryString=' . $queryString)
            );
        }
    }
}
