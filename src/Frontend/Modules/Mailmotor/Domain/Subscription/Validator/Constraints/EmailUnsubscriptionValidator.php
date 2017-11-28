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
class EmailUnsubscriptionValidator extends ConstraintValidator
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
        // There are already violations thrown, so we return immediately
        if (count($this->context->getViolations()) > 0) {
            return;
        }

        try {
            // The email doesn't exists in the mailing list
            if (!$this->subscriber->exists(
                $value,
                Model::get('fork.settings')->get('Mailmotor', 'list_id_' . Locale::frontendLanguage())
            )) {
                $this->context->buildViolation($constraint->notExistsMessage)->addViolation();
            } elseif ($this->subscriber->isUnsubscribed($value)) {
                $this->context->buildViolation($constraint->alreadyUnsubscribedMessage)->addViolation();
            }
        } catch (NotImplementedException $e) {
            // do nothing when no mail-engine is chosen in the Backend
        }
    }
}
