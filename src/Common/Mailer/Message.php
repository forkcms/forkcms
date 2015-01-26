<?php

namespace Common\Mailer;

use \TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;
use Frontend\Core\Engine\Template;
use Backend\Core\Engine\Template as BackendTemplate;
use Common\Uri;

/**
 * This class will send mails
 *
 * @author Wouter Sioen <wouter@sumocoders.be>
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
     * @return Swift_Message
     */
    public static function newInstance($subject = null, $body = null, $contentType = null, $charset = null)
    {
        return new self($subject, $body, $contentType, $charset);
    }

    /**
     * Parses a SpoonTemplate with the wanted variables
     *
     * @param  string $template
     * @param  array  $variables
     * @param  bool   $addUTM
     * @return Message
     */
    public function parseHtml($template, $variables, $addUTM)
    {
        $html = $this->getTemplateContent($template, $variables);
        $html = $this->relativeToAbsolute($html);

        if ($addUTM === true) {
            $html = $this->addUTM($html, $this->getSubject());
        }

        $this->setBody($html, 'text/html');

        return $this;
    }

    /**
     * Attach multiple attachments to this message
     *
     * @param  array $attachments
     * @return Message
     */
    public function addAttachments($attachments)
    {
        if (!empty($attachments)) {
            // add attachments one by one
            foreach ($attachments as $attachment) {
                // only add existing files
                if (is_file($attachment)) {
                    $message->attach(\Swift_Attachment::fromPath($attachment));
                }
            }
        }

        return $this;
    }

    /**
     * Add plaintext content as fallback for the html
     *
     * @param  string $content
     * @return Message
     */
    public function setPlainText($content)
    {
        if ($content !== null) {
            $message->addPart($content, 'text/plain');
        }

        return $this;
    }

    /**
     * @param string $html    The html to convert links in.
     * @param string $subject The subject of the mail
     * @return string
     */
    private function addUTM($html, $subject)
    {
        // match links
        $matches = array();
        preg_match_all('/href="(http:\/\/(.*))"/iU', $html, $matches);

        // any links?
        $utm = array('utm_source' => 'mail', 'utm_medium' => 'email', 'utm_campaign' => Uri::getUrl($subject));
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
     * @param string $template  The template to use.
     * @param array  $variables The variables to assign.
     * @return string
     */
    private function getTemplateContent($template, $variables = null)
    {
        // new template instance
        $tpl = null;
        if (APPLICATION === 'Backend') {
            $tpl = new BackendTemplate(false);
        } else {
            $tpl = new Template(false);
        }

        // set some options
        $tpl->setForceCompile(true);

        // variables were set
        if (!empty($variables)) {
            $tpl->assign($variables);
        }

        // grab the content
        $content = $tpl->getContent($template);

        // replace internal links/images
        $search = array('href="/', 'src="/');
        $replace = array('href="' . SITE_URL . '/', 'src="' . SITE_URL . '/');
        $content = str_replace($search, $replace, $content);

        // create instance
        $cssToInlineStyles = new CssToInlineStyles();

        // set some properties
        $cssToInlineStyles->setHTML($content);
        $cssToInlineStyles->setUseInlineStylesBlock(true);
        $cssToInlineStyles->setEncoding(SPOON_CHARSET);

        // return the content
        return (string) $cssToInlineStyles->convert();
    }

    /**
     * @param string $html  The html to convert links in.
     * @return string
     */
    private function relativeToAbsolute($html)
    {
        // get internal links
        $matches = array();
        preg_match_all('|href="/(.*)"|i', $html, $matches);

        // any links?
        if (!empty($matches[0])) {
            $search = array();
            $replace = array();

            // loop the links
            foreach ($matches[0] as $key => $link) {
                $search[] = $link;
                $replace[] = 'href="' . SITE_URL . '/' . $matches[1][$key] . '"';
            }

            $html = str_replace($search, $replace, $html);
        }

        return $html;
    }
}
