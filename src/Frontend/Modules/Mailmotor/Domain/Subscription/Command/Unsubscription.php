<?php

namespace App\Frontend\Modules\Mailmotor\Domain\Subscription\Command;

use App\Frontend\Core\Language\Locale;
use Symfony\Component\Validator\Constraints as Assert;
use App\Frontend\Modules\Mailmotor\Domain\Subscription\Validator\Constraints as MailingListAssert;

final class Unsubscription
{
    /**
     * @var string
     *
     * @Assert\NotBlank(message="err.FieldIsRequired")
     * @Assert\Email(message="err.EmailIsInvalid")
     * @MailingListAssert\EmailUnsubscription
     */
    public $email;

    /**
     * @var Locale
     */
    public $locale;

    public function __construct(Locale $locale, string $email = null)
    {
        $this->locale = $locale;
        $this->email = $email;
    }
}
