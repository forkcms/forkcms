<?php

namespace Backend\Core\Cronjobs;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\Cronjob;

/**
 * This is the cronjob to send the queued emails.
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Davy Hellemans <davy@spoon-library.com>
 */
class SendQueuedEmails extends Cronjob
{
    /**
     * Execute the action
     */
    public function execute()
    {
        // set busy file
        $this->setBusyFile();

        // send all queued e-mails
        $mailer = $this->get('mailer');
        foreach ($mailer->getQueuedMailIds() as $id) {
            $mailer->send($id);
        }

        // remove busy file
        $this->clearBusyFile();
    }
}
