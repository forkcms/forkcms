<?php

namespace Frontend\Modules\Mailmotor\Widgets;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Core\Engine\Form as FrontendForm;
use Frontend\Core\Engine\Navigation as FrontendNavigation;

/**
 * This is a widget with the subscribe form
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class Subscribe extends FrontendBaseWidget
{
    /**
     * Execute the extra
     */
    public function execute()
    {
        parent::execute();
        $this->loadTemplate();
        $this->loadForm();
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        $this->frm = new FrontendForm('subscribe', null, null, 'subscribeForm');
        $this->frm->setAction(
            FrontendNavigation::getURLForBlock('Mailmotor', 'Subscribe')
        );
        $this->frm->addText('email')
            ->setAttributes(array('required' => null, 'type' => 'email'));
        $this->frm->parse($this->tpl);
        $this->tpl->assign('formToken', $this->frm->getToken());
    }
}
