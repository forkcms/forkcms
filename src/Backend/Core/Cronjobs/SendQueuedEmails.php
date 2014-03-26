<?php

namespace Backend\Core\Cronjobs;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\Cronjob;
use Backend\Core\Engine\Mailer;

/**
 * This is the cronjob to send the queued emails.
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Davy Hellemans <davy@spoon-library.com>
 */
class BackendCoreCronjobSendQueuedEmails extends Cronjob
{
    /**
     * Execute the action
     */
    public function execute()
    {
        // set busy file
        $this->setBusyFile();

        // send all queued e-mails
        foreach(Mailer::getQueuedMailIds() as $id) {
            Mailer::send($id);
        }

        // remove busy file
        $this->clearBusyFile();
    }
}
