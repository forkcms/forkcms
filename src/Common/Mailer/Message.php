<?php

namespace Common\Mailer;

use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;
use Frontend\Core\Engine\Model;
use Frontend\Core\Engine\TwigTemplate as FrontendTemplate;
use Backend\Core\Engine\TwigTemplate as BackendTemplate;
use Common\Uri;

/**
 * This class will send mails
 */
class Message extends \Swift_Message
{
    /**
     * Create a new Message.
     *
     * Details may be optionally passed into the constructor.
     *
     * @param string $subject
     * @param string $body
     * @param string $contentType
     * @param string $charset
     */
    public function __construct($subject = null, $body = null, $contentType = null, $charset = null)
    {
        parent::__construct($subject, $body, $contentType, $charset);
    }

    /**
     * Create a new Message.
     *
     * @param string $subject
     * @param string $body
     * @param string $contentType
     * @param string $charset
     *
     * @return Message
     */
    public static function newInstance($subject = null, $body = null, $contentType = null, $charset = null)
    {
        return new self($subject, $body, $contentType, $charset);
    }

    /**
     * Parses a TwigTemplate with the wanted variables
     *
     * @param  string  $template
     * @param  array   $variables
     * @param  bool    $addUTM
     *
     * @return Message
     */
    public function parseHtml($template, $variables, $addUTM = false)
    {
        $html = $this->getTemplateContent($template, $variables);
        $html = $this->relativeToAbsolute($html);
        $html = $this->cssToInlineStyles($html);

        if ($addUTM === true) {
            $html = $this->addUTM($html, $this->getSubject());
        }

        $this->setBody($html, 'text/html');

        return $this;
    }

    /**
     * Attach multiple attachments to this message
     *
     * @param  array   $attachments
     *
     * @return Message
     */
    public function addAttachments($attachments)
    {
        if (!empty($attachments)) {
            // add attachments one by one
            foreach ($attachments as $attachment) {
                // only add existing files
                if (is_file($attachment)) {
                    $this->attach(\Swift_Attachment::fromPath($attachment));
                }
            }
        }

        return $this;
    }

    /**
     * Add plaintext content as fallback for the html
     *
     * @param  string  $content
     *
     * @return Message
     */
    public function setPlainText($content)
    {
        if ($content !== null) {
            $this->addPart($content, 'text/plain');
        }

        return $this;
    }

    /**
     * @param  string $html    The html to convert links in.
     * @param  string $subject The subject of the mail
     *
     * @return string
     */
    private function addUTM($html, $subject)
    {
        // match links
        $matches = array();
        preg_match_all('/href="(http:\/\/(.*))"/iU', $html, $matches);

        // any links?
        $utm = array(
            'utm_source' => 'mail',
            'utm_medium' => 'email',
            'utm_campaign' => Uri::getUrl($subject),
        );
        if (isset($matches[0]) && !empty($matches[0])) {
            $searchLinks = array();
            $replaceLinks = array();

            // loop old links
            foreach ($matches[1] as $i => $link) {
                $searchLinks[] = $matches[0][$i];
                $replaceLinks[] = 'href="' . Model::addURLParameters($link, $utm) . '"';
            }

            $html = str_replace($searchLinks, $replaceLinks, $html);
        }

        return $html;
    }

    /**
     * Returns the content from a given template
     *
     * @param  string $template  The template to use.
     * @param  array  $variables The variables to assign.
     *
     * @return string
     */
    private function getTemplateContent($template, $variables = null)
    {
        // new template instance
        $tpl = null;
        if (APPLICATION === 'Backend') {
            $tpl = new BackendTemplate(false);
        } else {
            return Model::get('templating')->render(
                $template,
                $variables
            );
        }

        // set some options
        $tpl->setForceCompile();

        // variables were set
        if (!empty($variables)) {
            $tpl->assign($variables);
        }

        // grab the content
        return $tpl->getContent($template);
    }

    /**
     * Converts all css to inline styles
     *
     * @param  string $html
     *
     * @return string
     */
    private function cssToInlineStyles($html)
    {
        $charset = Model::getContainer()->getParameter('kernel.charset');

        $cssToInlineStyles = new CssToInlineStyles();
        $cssToInlineStyles->setHTML($html);
        $cssToInlineStyles->setUseInlineStylesBlock(true);
        $cssToInlineStyles->setEncoding($charset);

        return (string) $cssToInlineStyles->convert();
    }

    /**
     * Replace internal links and images to absolute links
     *
     * @param  string $html The html to convert links in.
     *
     * @return string
     */
    private function relativeToAbsolute($html)
    {
        // replace internal links/images
        $search = array('href="/', 'src="/');
        $replace = array('href="' . SITE_URL . '/', 'src="' . SITE_URL . '/');

        return str_replace($search, $replace, $html);
    }
}
