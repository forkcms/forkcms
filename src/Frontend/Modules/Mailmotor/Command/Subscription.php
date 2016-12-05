<?php

namespace Frontend\Modules\Mailmotor\Command;

/*
 * This file is part of the Fork CMS Mailmotor Module from SIESQO.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Language\Locale;
use Symfony\Component\Validator\Constraints as Assert;
use Frontend\Modules\Mailmotor\Validator\Constraints as MailingListAssert;

final class Subscription
{
    /**
     * @var string
     *
     * @Assert\NotBlank(message="err.FieldIsRequired")
     * @Assert\Email
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

    /**
     * @param Locale $locale
     * @param null|string $email
     */
    public function __construct(
        Locale $locale,
        $email = null
    ) {
        $this->locale = $locale;
        $this->email = $email;
    }
}
