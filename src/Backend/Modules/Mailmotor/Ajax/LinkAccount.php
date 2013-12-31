<?php

namespace Backend\Modules\Mailmotor\Ajax;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;

/**
 * This checks if a CampaignMonitor account exists or not, and links it if it does
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class LinkAccount extends BackendBaseAJAXAction
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        // get parameters
        $url = \SpoonFilter::getPostValue('url', null, '');
        $username = \SpoonFilter::getPostValue('username', null, '');
        $password = \SpoonFilter::getPostValue('password', null, '');

        // filter out the 'http://' from the URL
        if (strpos($url, 'http://') !== false) {
            $url = str_replace('http://', '', $url);
        }
        if (strpos($url, 'https://') !== false) {
            $url = str_replace('https://', '', $url);
        }

        // init validation
        $errors = array();

        // validate input
        if (empty($url)) {
            $errors['url'] = BL::err('NoCMAccountCredentials');
        }
        if (empty($username)) {
            $errors['username'] = BL::err('NoCMAccountCredentials');
        }
        if (empty($password)) {
            $errors['password'] = BL::err('NoCMAccountCredentials');
        }

        // got errors
        if (!empty($errors)) {
            $this->output(self::OK, array('errors' => $errors), 'form contains errors');
        } else {
            try {
                // check if the CampaignMonitor class exists
                if (!is_file(PATH_LIBRARY . '/external/campaignmonitor.php')) {
                    throw new \Exception(BL::err(
                        'ClassDoesNotExist'
                    ));
                }

                // require CampaignMonitor class
                require_once PATH_LIBRARY . '/external/campaignmonitor.php';

                // init CampaignMonitor object
                new \CampaignMonitor($url, $username, $password, 10);

                // save the new data
                BackendModel::setModuleSetting($this->getModule(), 'cm_url', $url);
                BackendModel::setModuleSetting($this->getModule(), 'cm_username', $username);
                BackendModel::setModuleSetting($this->getModule(), 'cm_password', $password);

                // account was linked
                BackendModel::setModuleSetting($this->getModule(), 'cm_account', true);

                // trigger event
                BackendModel::triggerEvent($this->getModule(), 'after_account_linked');

                // CM was successfully initialized
                $this->output(
                    self::OK,
                    array('message' => 'account-linked'),
                    BL::msg('AccountLinked', $this->getModule())
                );
            } catch (\Exception $e) {
                // timeout occurred
                if ($e->getMessage() == 'Error Fetching http headers') {
                    $this->output(
                        self::BAD_REQUEST,
                        null,
                        BL::err('CmTimeout', $this->getModule())
                    );
                }

                // other error
                $this->output(
                    self::ERROR,
                    array('field' => 'url'),
                    sprintf(BL::err('CampaignMonitorError', $this->getModule()), $e->getMessage())
                );
            }
        }
    }
}
