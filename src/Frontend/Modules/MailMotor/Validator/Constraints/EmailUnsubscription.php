<?php

namespace Frontend\Modules\MailMotor\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class EmailUnsubscription extends Constraint
{
    public $notExistsMessage = 'err.EmailNotExistsInMailingList';
    public $alreadyUnsubscribedMessage = 'err.EmailIsAlreadyUnsubscribedInMailingList';
}
