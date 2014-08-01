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
use Backend\Modules\Mailmotor\Engine\Model as BackendMailmotorModel;

/**
 * This is the ajax-action to update a campaign
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class EditCampaign extends BackendBaseAJAXAction
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        // get parameters
        $id = \SpoonFilter::getPostValue('id', null, '', 'int');
        $name = trim(\SpoonFilter::getPostValue('value', null, '', 'string'));

        // validate
        if ($name == '') {
            $this->output(self::BAD_REQUEST, null, 'no name provided');
        } else {
            // get existing id
            $existingId = BackendMailmotorModel::getCampaignId($name);

            // validate
            if ($existingId !== 0 && $id !== $existingId) {
                $this->output(
                    self::ERROR,
                    array('id' => $existingId, 'error' => true),
                    BL::err('CampaignExists', $this->getModule())
                );
            } else {
                // build array
                $item = array();
                $item['id'] = $id;
                $item['name'] = $name;
                $item['created_on'] = BackendModel::getUTCDate('Y-m-d H:i:s');

                // get page
                $rows = BackendMailmotorModel::updateCampaign($item);

                // trigger event
                BackendModel::triggerEvent($this->getModule(), 'edited_campaign', array('item' => $item));

                // output
                if ($rows !== 0) {
                    $this->output(
                        self::OK,
                        array('id' => $id),
                        BL::msg('CampaignEdited', $this->getModule())
                    );
                } else {
                    $this->output(self::ERROR, null, BL::err('CampaignNotEdited', $this->getModule()));
                }
            }
        }
    }
}
