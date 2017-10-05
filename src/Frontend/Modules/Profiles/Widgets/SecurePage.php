<?php

namespace Frontend\Modules\Profiles\Widgets;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Modules\Profiles\Engine\Authentication as FrontendProfilesAuthentication;
use Symfony\Component\Security\Core\Exception\InsufficientAuthenticationException;

/**
 * This is a widget to help you secure a page and make it only accessible for logged-in users.
 */
class SecurePage extends FrontendBaseWidget
{
    public function execute(): void
    {
        parent::execute();
        $this->loadTemplate();

        if (!FrontendProfilesAuthentication::isLoggedIn()) {
            throw new InsufficientAuthenticationException('You need to log in to access this page');
        }
    }
}
