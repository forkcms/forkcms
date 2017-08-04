<?php

namespace Frontend\Modules\Blog\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Language\Language as FL;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Core\Engine\Rss as FrontendRSS;
use Frontend\Core\Engine\RssItem as FrontendRSSItem;
use Frontend\Core\Engine\User as FrontendUser;
use Frontend\Modules\Blog\Engine\Model as FrontendBlogModel;

/**
 * This is the RSS-feed
 */
class Rss extends FrontendBaseBlock
{
    /**
     * The articles
     *
     * @var array
     */
    private $items;

    /**
     * The settings
     *
     * @var array
     */
    private $settings;

    public function execute(): void
    {
        parent::execute();
        $this->getData();
        $this->parse();
    }

    private function getData(): void
    {
        $this->items = FrontendBlogModel::getAll(30);
        $this->settings = $this->get('fork.settings')->getForModule('Blog');
    }

    private function parse(): void
    {
        // get vars
        $title = (isset($this->settings['rss_title_' . LANGUAGE])) ? $this->settings['rss_title_' . LANGUAGE] : $this->get('fork.settings')->get('Blog', 'rss_title_' . LANGUAGE, SITE_DEFAULT_TITLE);
        $link = SITE_URL . FrontendNavigation::getUrlForBlock('Blog');
        $description = (isset($this->settings['rss_description_' . LANGUAGE])) ? $this->settings['rss_description_' . LANGUAGE] : null;

        // create new rss instance
        $rss = new FrontendRSS($title, $link, $description);

        // loop articles
        foreach ($this->items as $item) {
            $title = $item['title'];
            $link = $item['full_url'];
            $description = ($item['introduction'] != '') ? $item['introduction'] : $item['text'];

            // meta is wanted
            if ($this->get('fork.settings')->get('Blog', 'rss_meta_' . LANGUAGE, true)) {
                // append meta
                $description .= '<div class="meta">' . "\n";
                $description .= '    <p><a href="' . $link . '" title="' . $title . '">' . $title . '</a> ' .
                                sprintf(
                                    FL::msg('WrittenBy'),
                                    FrontendUser::getBackendUser($item['user_id'])->getSetting('nickname')
                                );
                $description .= ' ' . FL::lbl('In') . ' <a href="' . $item['category_full_url'] . '" title="' .
                                $item['category_title'] . '">' . $item['category_title'] . '</a>.</p>' . "\n";

                // any tags
                if (isset($item['tags'])) {
                    // append tags-paragraph
                    $description .= '    <p>' . \SpoonFilter::ucfirst(FL::lbl('Tags')) . ': ';
                    $first = true;

                    // loop tags
                    foreach ($item['tags'] as $tag) {
                        // prepend separator
                        if (!$first) {
                            $description .= ', ';
                        }

                        // add
                        $description .= '<a href="' . $tag['full_url'] . '" rel="tag" title="' . $tag['name'] . '">' .
                                        $tag['name'] . '</a>';

                        // reset
                        $first = false;
                    }

                    // end
                    $description .= '.</p>' . "\n";
                }

                // end HTML
                $description .= '</div>' . "\n";
            }

            // create new instance
            $rssItem = new FrontendRSSItem($title, $link, $description);

            // set item properties
            $rssItem->setPublicationDate($item['publish_on']);
            $rssItem->addCategory($item['category_title']);
            $rssItem->setAuthor(FrontendUser::getBackendUser($item['user_id'])->getSetting('nickname'));

            // add item
            $rss->addItem($rssItem);
        }

        // output
        $rss->parse();
    }
}
