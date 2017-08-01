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
 */
class Rss extends \SpoonFeedRSS
{
    public function __construct(string $title, string $link, string $description, array $items = [])
    {
        // decode
        $title = \SpoonFilter::htmlspecialcharsDecode($title);
        $description = \SpoonFilter::htmlspecialcharsDecode($description);

        // call the parent
        parent::__construct(
            $title,
            Model::addUrlParameters(
                $link,
                ['utm_source' => 'feed', 'utm_medium' => 'rss', 'utm_campaign' => CommonUri::getUrl($title)]
            ),
            $description,
            $items
        );

        $siteTitle = \SpoonFilter::htmlspecialcharsDecode(
            Model::get('fork.settings')->get('Core', 'site_title_' . LANGUAGE)
        );

        // set feed properties
        $this->setLanguage(LANGUAGE);
        $this->setCopyright(\SpoonDate::getDate('Y') . ' ' . $siteTitle);
        $this->setGenerator($siteTitle);
        $this->setImage(SITE_URL . FRONTEND_CORE_URL . '/Layout/images/rss_image.png', $title, $link);

        // theme was set
        if (Model::get('fork.settings')->get('Core', 'theme', null) === null) {
            return;
        }

        // theme name
        $theme = Model::get('fork.settings')->get('Core', 'theme', 'Fork');

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

    public function setImage($url, $title, $link, $width = null, $height = null, $description = null): void
    {
        // add UTM-parameters
        $link = Model::addUrlParameters(
            $link,
            [
                'utm_source' => 'feed',
                'utm_medium' => 'rss',
                'utm_campaign' => CommonUri::getUrl($this->getTitle()),
            ]
        );

        // call the parent
        parent::setImage($url, $title, $link, $width, $height, $description);
    }
}
