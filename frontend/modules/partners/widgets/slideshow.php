<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is a widget for the partner slideshow
 *
 * @author Jelmer Prins <jelmer@sumocoders.be>
 */
class FrontendPartnersWidgetSlideshow extends FrontendBaseWidget
{
    /**
     * Execute the extra
     */
    public function execute()
    {
        parent::execute();
        $this->loadTemplate();
        $this->parse();
    }

    /**
     * Parse
     */
    private function parse()
    {
        $this->tpl->assign('partners', FrontendPartnersModel::getSlidersPartners($this->data['partners_widget_id']));
    }
}
