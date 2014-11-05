<?php

namespace Backend\Modules\Analytics;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use \Symfony\Component\HttpKernel\KernelInterface;

use Backend\Core\Engine\Base\Config as BackendBaseConfig;
use Backend\Core\Engine\Model as BackendModel;

/**
 * This is the configuration-object for the analytics module
 *
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 */
class Config extends BackendBaseConfig
{
    /**
     * The default action
     *
     * @var	string
     */
    protected $defaultAction = 'Index';

    /**
     * The disabled actions
     *
     * @var	array
     */
    protected $disabledActions = array();

    /**
     * Check if all required settings have been set
     *
     * @param \Symfony\Component\HttpKernel\KernelInterface $kernel
     * @param string $module The module.
     */
    public function __construct(KernelInterface $kernel, $module)
    {
        parent::__construct($kernel, $module);

        $error = false;
        $action = $this->getContainer()->has('url') ? $this->getContainer()->get('url')->getAction() : null;

        // analytics session token
        if (
            BackendModel::getModuleSetting('Analytics', 'session_token') === null ||
            BackendModel::getModuleSetting('Analytics', 'table_id') === null
        ) {
            $error = true;
        }

        // container has no url, so we are in cronjob
        if ($error && !$this->getContainer()->has('url')) {
            throw new \Exception('The settings for Analytics must be filled in to use this cronjob.');
        // missing settings, so redirect to the index-page to show a message (except on the index- and settings-page)
        } elseif ($error && $action != 'Settings' && $action != 'Index') {
            \SpoonHTTP::redirect(BackendModel::createURLForAction('Index'));
        }
    }
}
