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

## Redirects in frontend actions

We removed the `exit;` line in the redirect method, in favor of bubbling up the
redirect response up to the AppKernel, to be send just like any other response.

This means that you should return your redirects in your actions, instead of just
running `$this->redirect($url)`.

    // Old code
    $this->redirect($url);

    // New code (in execute method)
    return $this->redirect($url);

    // New code (in another method)
    use Symfony\Component\HttpFoundation\Response;

    ...

    public function execute()
    {
        parent::execute();

        $response = $this->getData();
        if ($response instanceof Response) {
            return $response;
        }
        $this->parse();
    }

    public function getData()
    {
        $data = SomeModel::getData();
        if ($data === null) {
            return $this->redirect(FrontendNavigation::getURL(404));
        }
    }
