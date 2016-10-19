<?php

namespace Backend;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This class will initiate the backend-application
 */
class Init extends \Common\Core\Init
{
    /**
     * {@inheritdoc}
     */
    protected $allowedTypes = array('Backend', 'BackendAjax', 'BackendCronjob', 'Console');

    /**
     * {@inheritdoc}
     */
    public function initialize($type)
    {
        parent::initialize($type);

        \SpoonFilter::disableMagicQuotes();
    }
}
