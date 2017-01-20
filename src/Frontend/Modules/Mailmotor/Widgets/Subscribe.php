<?php

namespace Frontend\Modules\Mailmotor\Widgets;

/*
 * This file is part of the Fork CMS Mailmotor Module from SIESQO.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Modules\Mailmotor\Command\Subscription;
use Frontend\Core\Language\Locale;

/**
 * This is a widget with the Subscribe form
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

        // Create the form
        $form = $this->createForm(
            $this->get('mailmotor.form.subscription'),
            new Subscription(
                Locale::frontendLanguage()
            )
        );

        $form->handleRequest($this->get('request'));

        if (!$form->isValid()) {
            $this->tpl->assign('form', $form->createView());

            if ($form->isSubmitted()) {
                $this->tpl->assign('mailmotorSubscribeHasFormError', true);
            }
            return;
        }
    }
}
