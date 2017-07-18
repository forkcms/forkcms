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
    private $tags = [];

    public function execute(): void
    {
        $this->loadTemplate();
        $this->getData();
        $this->parse();
    }

    private function getData(): void
    {
        $this->tags = FrontendTagsModel::getAll();
    }

    private function parse(): void
    {
        // make tags available
        $this->template->assign('tags', $this->tags);

        // tag-pages don't have any SEO-value, so don't index them
        $this->header->addMetaData(['name' => 'robots', 'content' => 'noindex, follow'], true);
    }
}
