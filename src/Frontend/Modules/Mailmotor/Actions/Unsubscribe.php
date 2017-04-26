<?php

namespace Frontend\Modules\Mailmotor\Actions;

/*
 * This file is part of the Fork CMS Mailmotor Module from SIESQO.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Core\Language\Locale;
use Frontend\Modules\Mailmotor\Command\Unsubscription;
use Frontend\Modules\Mailmotor\Event\NotImplementedUnsubscribedEvent;
use Frontend\Modules\Mailmotor\Form\UnsubscribeType;
use MailMotor\Bundle\MailMotorBundle\Exception\NotImplementedException;

/**
 * This is the Unsubscription-action for Mailmotor
 */
class Unsubscribe extends FrontendBaseBlock
{
    /**
     * Execute the extra
     */
    public function execute()
    {
        parent::execute();

        // Define email from the unsubscribe widget
        $email = $this->getEmail();

        // Create the form
        $form = $this->createForm(
            UnsubscribeType::class,
            new Unsubscription(
                Locale::frontendLanguage(),
                $email
            )
        );

        $form->handleRequest($this->get('request'));

        if (!$form->isValid()) {
            $this->tpl->assign('form', $form->createView());

            if ($form->isSubmitted()) {
                $this->tpl->assign('mailmotorUnsubscribeHasFormError', true);
            }

            $this->loadTemplate();
            $this->parse();

            return;
        }

        /** @var Unsubscription $unsubscription */
        $unsubscription = $form->getData();

        try {
            // The command bus will handle the unsubscription
            $this->get('command_bus')->handle($unsubscription);
        // fallback for when no mail-engine is chosen in the Backend
        } catch (NotImplementedException $e) {
            $this->get('event_dispatcher')->dispatch(
                NotImplementedUnsubscribedEvent::EVENT_NAME,
                new NotImplementedUnsubscribedEvent(
                    $unsubscription
                )
            );
        }

        return $this->redirect(
            FrontendNavigation::getURLForBlock(
                'Mailmotor',
                'Unsubscribe'
            )
            . '?unsubscribed=true'
            . '#mailmotorUnsubscribeForm'
        );
    }

    /**
     * Get email
     */
    public function getEmail()
    {
        // define email
        $email = null;

        // request contains an email
        if ($this->get('request')->request->get('email') != null) {
            $email = $this->get('request')->request->get('email');
        }

        return $email;
    }

    /**
     * Parse the data into the template
     */
    private function parse()
    {
        // form was unsubscribed?
        if ($this->URL->getParameter('unsubscribed') === 'true') {
            // show message
            $this->tpl->assign('mailmotorUnsubscribeIsSuccess', true);

            // hide form
            $this->tpl->assign('mailmotorUnsubscribeHideForm', true);
        }
    }
}
