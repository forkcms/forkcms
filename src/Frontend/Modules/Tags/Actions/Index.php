<?php

namespace Frontend\Modules\Tags\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Modules\Tags\Engine\Model as FrontendTagsModel;

/**
 * This is the index-action
 */
class Index extends FrontendBaseBlock
{
    /**
     * List of tags
     *
     * @var array
     */
    private $tags = array();

    /**
     * Execute the extra
     */
    public function execute()
    {
        $this->loadTemplate();
        $this->getData();
        $this->parse();
    }

    /**
     * Load the data from the database.
     */
    private function getData()
    {
        $this->tags = FrontendTagsModel::getAll();
    }

    /**
     * Parse the data into the template
     */
    private function parse()
    {
        // make tags available
        $this->tpl->assign('tags', $this->tags);

        // tag-pages don't have any SEO-value, so don't index them
        $this->header->addMetaData(array('name' => 'robots', 'content' => 'noindex, follow'), true);
    }
}
