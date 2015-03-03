UPGRADE FROM 3.8 to 3.9
=======================

## Swiftmailer

The `$this->get('mailer')->addEmail(<too much parameters>)` method has been removed
in favor of `$this->get('mailer')->send($message)`.

The $message parameter should be an instance of \Swift_Message. Fork provides an
extended version of this message object that will also parse SpoonTemplates. You can
use it like this:

    // create a message object and set all the needed properties
    $message = \Common\Mailer\Message::newInstance($subject)
        ->setFrom(array($fromEmail => $fromName))
        ->setTo(array($toEmail => $toName))
        ->setReplyTo(array($replyToEmail => $replyToName))
        ->parseHtml($template, $variables, $addUTM)
        ->setPlainText($plainText)
        ->addAttachments($attachments)
    ;

    // send it trough the mailer service
    $this->get('mailer')->send($message);

All occurences of sending emails in the core are replaced by this, so you can use
this as a reference.


## SPOON_DEBUG

SPOON_DEBUG is removed. From now on you need to check if DEBUG is on by using the kernel.debug parameter, f.e.

	if ($this->getContainer()->getParameter('kernel.debug')) { ...