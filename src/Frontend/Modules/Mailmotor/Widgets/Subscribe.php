<?php

namespace ForkCMS\Frontend\Modules\Mailmotor\Widgets;

use ForkCMS\Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use ForkCMS\Frontend\Modules\Mailmotor\Domain\Subscription\Command\Subscription;
use ForkCMS\Frontend\Core\Language\Locale;
use ForkCMS\Frontend\Modules\Mailmotor\Domain\Subscription\SubscribeType;

/**
 * This is a widget with the Subscribe form
 */
class Subscribe extends FrontendBaseWidget
{
    public function execute(): void
    {
        parent::execute();
        $this->loadTemplate();
        $form = $this->createForm(
            SubscribeType::class,
            new Subscription(Locale::frontendLanguage())
        );

        $form->handleRequest($this->getRequest());

        if ($form->isSubmitted() && $form->isValid()) {
            return;
        }

        $this->template->assign('form', $form->createView());

        if ($form->isSubmitted()) {
            $this->template->assign('mailmotorSubscribeHasFormError', true);
        }
    }
}
