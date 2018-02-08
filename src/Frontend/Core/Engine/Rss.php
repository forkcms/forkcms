<?php

namespace Frontend\Core\Engine;

use App\Component\Model\FrontendModel;
use App\Component\Uri\Uri;

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
            str_replace(
                '&',
                '&amp;',
                FrontendModel::addUrlParameters(
                    $link,
                    ['utm_source' => 'feed', 'utm_medium' => 'rss', 'utm_campaign' => Uri::getUrl($title)],
                    '&amp;'
                )
            ),
            $description,
            $items
        );

        $siteTitle = \SpoonFilter::htmlspecialcharsDecode(
            FrontendModel::get('forkcms.settings')->get('Core', 'site_title_' . LANGUAGE)
        );

        // set feed properties
        $this->setLanguage(LANGUAGE);
        $this->setCopyright(\SpoonDate::getDate('Y') . ' ' . $siteTitle);
        $this->setGenerator($siteTitle);
        $this->setImage(SITE_URL . FRONTEND_CORE_URL . '/Layout/images/rss_image.png', $title, $link);

        // theme was set
        if (FrontendModel::get('forkcms.settings')->get('Core', 'theme', null) === null) {
            return;
        }

        // theme name
        $theme = FrontendModel::get('forkcms.settings')->get('Core', 'theme', 'Fork');

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
        $link = FrontendModel::addUrlParameters(
            $link,
            [
                'utm_source' => 'feed',
                'utm_medium' => 'rss',
                'utm_campaign' => Uri::getUrl($this->getTitle()),
            ],
            '&amp;'
        );

        // call the parent
        parent::setImage($url, $title, $link, $width, $height, $description);
    }
}
