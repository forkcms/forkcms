<?php

namespace Frontend\Modules\MailMotor\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class EmailSubscription extends Constraint
{
    public $alreadySubscribedMessage = 'err.EmailAlreadySubscribedInMailingList';
}
