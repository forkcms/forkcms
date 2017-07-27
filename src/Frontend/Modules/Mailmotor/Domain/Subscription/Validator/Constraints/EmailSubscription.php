<?php

namespace Frontend\Modules\Mailmotor\Domain\Subscription\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class EmailSubscription extends Constraint
{
    public $alreadySubscribedMessage = 'err.EmailAlreadySubscribedInMailingList';
}
