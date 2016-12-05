<?php

namespace Backend\Modules\Mailmotor\Actions;

/*
 * This file is part of the Fork CMS Mailmotor Module from SIESQO.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionIndex;
use Backend\Core\Engine\Model;
use Backend\Modules\Mailmotor\Command\SaveSettings;
use Backend\Modules\Mailmotor\Event\SettingsSavedEvent;

/**
 * This redirects to settings
 */
final class Index extends ActionIndex
{
    /**
     * Execute
     */
    public function execute()
    {
        parent::execute();

        return $this->redirect(
            Model::createURLForAction(
                'Settings'
            )
        );
    }
}
