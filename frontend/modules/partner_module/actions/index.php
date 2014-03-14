<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is slideshow widget action
 *
 * @author Jelmer Prins <jelmer@sumocoders.be>
 */
class FrontendPartnerModuleIndex extends FrontendBaseBlock
{
    /**
     * The articles
     *
     * @var    array
     */
    private $items;

    /**
     * Execute the extra
     */
    public function execute()
    {
        parent::execute();
        $this->loadTemplate();
        $this->getData();
        $this->parse();
    }

    /**
     * Load the data, don't forget to validate the incoming data
     */
    private function getData()
    {
        $this->items = FrontendPartnerModuleModel::getAll();
    }

    /**
     * Parse the data into the template
     */
    protected function parse()
    {
        $this->tpl->assign('items', $this->items);
        $this->parsePagination();
    }
}
