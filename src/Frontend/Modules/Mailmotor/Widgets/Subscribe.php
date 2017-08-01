<?php

namespace Frontend\Modules\Mailmotor\Widgets;

/*
 * This file is part of the Fork CMS Mailmotor Module from SIESQO.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Modules\Mailmotor\Domain\Subscription\Command\Subscription;
use Frontend\Core\Language\Locale;
use Frontend\Modules\Mailmotor\Domain\Subscription\SubscribeType;

/**
 * This is a widget with the Subscribe form
 */
class Subscribe extends FrontendBaseWidget
{
    public function execute(): void
    {
        // call parent
        parent::execute();

        // load template
        $this->loadTemplate();

        // Create the form
        $form = $this->createForm(
            SubscribeType::class,
            new Subscription(
                Locale::frontendLanguage()
            )
        );

        $form->handleRequest($this->getRequest());

        if (!$form->isSubmitted() || !$form->isValid()) {
            $this->template->assign('form', $form->createView());

            if ($form->isSubmitted()) {
                $this->template->assign('mailmotorSubscribeHasFormError', true);
            }

            return;
        }
    }
}
