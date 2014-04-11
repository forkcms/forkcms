<?php

namespace Backend\Core\Cronjobs;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

use Backend\Core\Engine\Base\Cronjob;
use Backend\Core\Engine\Exception;
use Backend\Core\Engine\Model as BackendModel;

/**
 * This is the cronjob that processes the queued hooks.
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class ProcessQueuedHooks extends Cronjob
{
    /**
     * Execute the action
     */
    public function execute()
    {
        // no timelimit
        set_time_limit(0);

        // get database
        $db = BackendModel::getContainer()->get('database');

        // create log
        $log = BackendModel::getContainer()->get('logger');

        // get process-id
        $pid = getmypid();

        // store PID
        $fs = new Filesystem();
        $fs->dumpFile(
            BACKEND_CACHE_PATH . '/Hooks/pid',
            $pid
        );

        while (true) {
            // get 1 item
            $item = $db->getRecord(
                'SELECT *
                 FROM hooks_queue
                 WHERE status = ?
                 LIMIT 1',
                array('queued')
            );

            // any item?
            if (!empty($item)) {
                // init var
                $processedSuccessfully = true;

                // set item as busy
                $db->update('hooks_queue', array('status' => 'busy'), 'id = ?', array($item['id']));

                // unserialize data
                $item['callback'] = unserialize($item['callback']);
                $item['data'] = unserialize($item['data']);

                // check if the item is callable
                if (!is_callable($item['callback'])) {
                    // in debug mode we want to know if there are errors
                    if (SPOON_DEBUG) {
                        throw new Exception('Invalid callback!');
                    }

                    // set to error state
                    $db->update('hooks_queue', array('status' => 'error'), 'id = ?', $item['id']);

                    // reset state
                    $processedSuccessfully = false;

                    $log->info('Callback (' . serialize($item['callback']) . ') failed.');
                }

                try {
                    $log->info('Callback (' . serialize($item['callback']) . ') called.');

                    // call the callback
                    $return = call_user_func($item['callback'], $item['data']);

                    // failed?
                    if ($return === false) {
                        // set to error state
                        $db->update('hooks_queue', array('status' => 'error'), 'id = ?', $item['id']);

                        // reset state
                        $processedSuccessfully = false;

                        $log->info('Callback (' . serialize($item['callback']) . ') failed.');
                    }
                } catch (Exception $e) {
                    // set to error state
                    $db->update('hooks_queue', array('status' => 'error'), 'id = ?', $item['id']);

                    // reset state
                    $processedSuccessfully = false;

                    // logging when we are in debugmode
                    if (SPOON_DEBUG) {
                        $log->err('Callback (' . serialize($item['callback']) . ') failed.');
                    }
                }

                // everything went fine so delete the item
                if ($processedSuccessfully) {
                    $db->delete('hooks_queue', 'id = ?', $item['id']);
                }

                // logging when we are in debugmode
                if (SPOON_DEBUG) {
                    $log->info('Callback (' . serialize($item['callback']) . ') finished.');
                }
            } else {
                $fs = new Filesystem();
                $fs->remove(BACKEND_CACHE_PATH . '/Hooks/pid');

                // stop the script
                exit;
            }
        }
    }
}
