<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This action will add a post to the blog module.
 *
 * @author Jelmer Prins <jelmer@sumocoders.be>
 */
final class BackendPartnerModuleConfig extends BackendBaseConfig
{
    /**
     * The default action
     *
     * @var    string
     */
    protected $defaultAction = 'index';

    /**
     * The disabled actions
     *
     * @var    array
     */
    protected $disabledActions = array();
}
