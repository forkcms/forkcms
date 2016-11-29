<?php

namespace Frontend\Modules\Mailmotor\Widgets;

/*
 * This file is part of the Fork CMS Mailmotor Module from SIESQO.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;

/**
 * This is a widget with the Subscribe form
 *
 * @author Jeroen Desloovere <jeroen@siesqo.be>
 */
class Subscribe extends FrontendBaseWidget
{
    /**
     * Execute the extra
     *
     * @return void
     */
    public function execute()
    {
        // call parent
        parent::execute();

        // load template
        $this->loadTemplate();
    }
}
