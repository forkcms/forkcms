<?php

namespace Frontend\Modules\Mailmotor\Command;

/*
 * This file is part of the Fork CMS Mailmotor Module from SIESQO.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\Validator\Constraints as Assert;
use Frontend\Modules\Mailmotor\Validator\Constraints as MailingListAssert;

final class Unsubscription
{
    /**
     * @var string
     *
     * @Assert\NotBlank(message="err.FieldIsRequired")
     * @Assert\Email
     * @MailingListAssert\EmailUnsubscription
     */
    public $email;

    /**
     * @var string
     */
    public $language;

    /**
     * @param mixed null|string $email
     * @param string $language
     */
    public function __construct($email = null, $language)
    {
        $this->language = $language;
        $this->email = $email;
    }
}
