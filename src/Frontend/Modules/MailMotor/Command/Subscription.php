<?php

namespace Frontend\Modules\MailMotor\Command;

/*
 * This file is part of the Fork CMS MailMotor Module from SIESQO.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\Validator\Constraints as Assert;
use Frontend\Modules\MailMotor\Validator\Constraints as MailingListAssert;

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
     * @var string
     */
    public $language;

    /**
     * @var array
     *
     * @Assert\NotBlank(groups={"has_interests"}, message="err.MailingListInterestsIsRequired")
     */
    public $interests;

    /**
     * @param mixed null|string $email
     * @param string $language
     */
    public function __construct(
        $email = null,
        $language
    ) {
        $this->language = $language;
        $this->email = $email;
    }
}
