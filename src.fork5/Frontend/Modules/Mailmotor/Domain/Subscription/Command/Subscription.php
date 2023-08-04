<?php

namespace Frontend\Modules\Mailmotor\Domain\Subscription\Command;

use Frontend\Core\Language\Locale;
use Symfony\Component\Validator\Constraints as Assert;
use Frontend\Modules\Mailmotor\Domain\Subscription\Validator\Constraints as MailingListAssert;

final class Subscription
{
    /**
     * @var string
     *
     * @Assert\NotBlank(message="err.FieldIsRequired")
     * @Assert\Email(message="err.EmailIsInvalid")
     * @MailingListAssert\EmailSubscription
     */
    public $email;

    /**
     * @var Locale
     */
    public $locale;

    /**
     * @var array
     *
     * @Assert\NotBlank(groups={"has_interests"}, message="err.MailingListInterestsIsRequired")
     */
    public $interests;

    public function __construct(Locale $locale, string $email = null)
    {
        $this->locale = $locale;
        $this->email = $email;
    }
}
