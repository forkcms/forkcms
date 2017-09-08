<?php

namespace Frontend\Modules\Mailmotor\Domain\Subscription\Validator\Constraints;

use Frontend\Core\Engine\Model;
use Frontend\Core\Language\Locale;
use MailMotor\Bundle\MailMotorBundle\Helper\Subscriber;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use MailMotor\Bundle\MailMotorBundle\Exception\NotImplementedException;

/**
 * @Annotation
 */
class EmailSubscriptionValidator extends ConstraintValidator
{
    /**
     * @var Subscriber
     */
    protected $subscriber;

    public function setSubscriber(Subscriber $subscriber): void
    {
        $this->subscriber = $subscriber;
    }

    public function validate($value, Constraint $constraint): void
    {
        try {
            // The email is already in our mailing list
            if ($this->subscriber->isSubscribed(
                $value,
                Model::get('fork.settings')->get('Mailmotor', 'list_id_' . Locale::frontendLanguage())
            )) {
                $this->context->buildViolation($constraint->alreadySubscribedMessage)->addViolation();
            }
            // fallback for when no mail-engine is chosen in the Backend
        } catch (NotImplementedException $e) {
            // do nothing
        }
    }
}
