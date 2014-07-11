<?php

namespace Backend\Modules\Analytics\Ajax;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Analytics\Engine\Model as BackendAnalyticsModel;

/**
 * This edit-action will check the status using Ajax
 *
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 */
class CheckStatus extends BackendBaseAJAXAction
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();
        $page = trim(\SpoonFilter::getPostValue('page', null, ''));
        $identifier = trim(\SpoonFilter::getPostValue('identifier', null, ''));
        $fs = new Filesystem();

        // validate
        if ($page == '' || $identifier == '') {
            $this->output(self::BAD_REQUEST, null, 'No page provided.');
        } else {
            // validated
            $filename = BACKEND_CACHE_PATH . '/Analytics/' . $page . '_' . $identifier . '.txt';

            if ($fs->exists($filename)) {
                $status = file_get_contents($filename);
            } else {
                $status = false;
            }

            // no file - create one
            if ($status === false) {
                // create file with initial counter
                $fs->dumpFile($filename, 'missing1');

                // return status
                $this->output(self::OK, array('status' => false), 'Temporary file was missing. We created one.');
            } elseif (strpos($status, 'busy') !== false) {
                // busy status: get counter
                $counter = (int) substr($status, 4) + 1;

                // file's been busy for more than hundred cycles - just stop here
                if ($counter > 100) {
                    // remove file
                    \SpoonFile::delete($filename);

                    // return status
                    $this->output(
                        self::ERROR,
                        array('status' => 'timeout'),
                        'Error while retrieving data - the script took too long to retrieve data.'
                    );
                    return;
                }

                // change file content to increase counter
                $fs->dumpFile($filename, 'busy' . $counter);

                // return status
                $this->output(
                    self::OK,
                    array('status' => 'busy', 'temp' => $status),
                    'Data is being retrieved. (' . $counter . ')'
                );
            } elseif ($status == 'unauthorized') {
                // unauthorized status: remove file

                if (is_file($filename)) {
                    $fs->remove($filename);
                }

                // remove all parameters from the module settings
                BackendModel::setModuleSetting($this->getModule(), 'session_token', null);
                BackendModel::setModuleSetting($this->getModule(), 'account_name', null);
                BackendModel::setModuleSetting($this->getModule(), 'table_id', null);
                BackendModel::setModuleSetting($this->getModule(), 'profile_title', null);

                BackendAnalyticsModel::removeCacheFiles();
                BackendAnalyticsModel::clearTables();

                $this->output(self::OK, array('status' => 'unauthorized'), 'No longer authorized.');
            } elseif ($status == 'done') {
                // done status: remove file
                if (is_file($filename)) {
                    $fs->remove($filename);
                }

                // return status
                $this->output(self::OK, array('status' => 'done'), 'Data retrieved.');
            } elseif (strpos($status, 'missing') !== false) {
                // missing status: get counter
                $counter = (int) substr($status, 7) + 1;

                // file's been missing for more than ten cycles - just stop here
                if ($counter > 10) {
                    if (is_file($filename)) {
                        $fs->remove($filename);
                    }
                    $this->output(
                        self::ERROR,
                        array('status' => 'missing'),
                        'Error while retrieving data - file was never created.'
                    );
                }

                // change file content to increase counter
                $fs->dumpFile($filename, 'missing' . $counter);

                // return status
                $this->output(
                    self::OK,
                    array('status' => 'busy'),
                    'Temporary file was still in status missing. (' . $counter . ')'
                );
            } else {
                /* FALLBACK - SOMETHING WENT WRONG */
                if (is_file($filename)) {
                    $fs->remove($filename);
                }
                $this->output(
                    self::ERROR,
                    array('status' => 'error', 'a' => ($status == 'done')),
                    'Error while retrieving data.'
                );
            }
        }
    }
}
