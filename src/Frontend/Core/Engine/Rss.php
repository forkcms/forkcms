<?php

namespace Frontend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Common\Uri as CommonUri;

/**
 * Frontend RSS class.
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Dieter Vanden Eynde <dieter@dieterve.be>
 */
class Rss extends \SpoonFeedRSS
{
    /**
     * The default constructor
     *
     * @param string $title       The title off the feed.
     * @param string $link        The link of the feed.
     * @param string $description The description of the feed.
     * @param array  $items       An array with SpoonRSSItems.
     */
    public function __construct($title, $link, $description, array $items = array())
    {
        // decode
        $title = \SpoonFilter::htmlspecialcharsDecode($title);
        $description = \SpoonFilter::htmlspecialcharsDecode($description);

        // call the parent
        parent::__construct(
            $title,
            Model::addURLParameters(
                $link,
                array('utm_source' => 'feed', 'utm_medium' => 'rss', 'utm_campaign' => CommonUri::getUrl($title))
            ),
            $description,
            $items
        );

        $siteTitle = \SpoonFilter::htmlspecialcharsDecode(
            Model::getModuleSetting('Core', 'site_title_' . FRONTEND_LANGUAGE)
        );

        // set feed properties
        $this->setLanguage(FRONTEND_LANGUAGE);
        $this->setCopyright(\SpoonDate::getDate('Y') . ' ' . $siteTitle);
        $this->setGenerator($siteTitle);
        $this->setImage(SITE_URL . FRONTEND_CORE_URL . '/Layout/images/rss_image.png', $title, $link);

        // theme was set
        if (Model::getModuleSetting('Core', 'theme', null) != null) {
            // theme name
            $theme = Model::getModuleSetting('Core', 'theme', null);

            // theme rss image exists
            if (is_file(PATH_WWW . '/src/Frontend/Themes/' . $theme . '/Core/images/rss_image.png')) {
                // set rss image
                $this->setImage(
                    SITE_URL . '/src/Frontend/Themes/' . $theme . '/Core/images/rss_image.png',
                    $title,
                    $link
                );
            }
        }
    }

    /**
     * Set the image for the feed.
     *
     * @param string $URL         URL of the image.
     * @param string $title       Title of the image.
     * @param string $link        Link of the image.
     * @param int    $width       Width of the image.
     * @param int    $height      Height of the image.
     * @param string $description Description of the image.
     */
    public function setImage($URL, $title, $link, $width = null, $height = null, $description = null)
    {
        // add UTM-parameters
        $link = Model::addURLParameters(
            $link,
            array(
                'utm_source' => 'feed',
                'utm_medium' => 'rss',
                'utm_campaign' => CommonUri::getUrl($this->getTitle())
            )
        );

        // call the parent
        parent::setImage($URL, $title, $link, $width, $height, $description);
    }
}
