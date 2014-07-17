<?php

namespace Frontend\Modules\Blog\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Language as FL;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Core\Engine\Rss as FrontendRSS;
use Frontend\Core\Engine\RssItem as FrontendRSSItem;
use Frontend\Core\Engine\User as FrontendUser;
use Frontend\Modules\Blog\Engine\Model as FrontendBlogModel;

/**
 * This is the RSS-feed
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 */
class Rss extends FrontendBaseBlock
{
    /**
     * The articles
     *
     * @var    array
     */
    private $items;

    /**
     * Execute the extra
     */
    public function execute()
    {
        parent::execute();
        $this->getData();
        $this->parse();
    }

    /**
     * Load the data, don't forget to validate the incoming data
     */
    private function getData()
    {
        $this->items = FrontendBlogModel::getAll(30);
    }

    /**
     * Parse the data into the template
     */
    private function parse()
    {
        // get vars
        $title = FrontendModel::getModuleSetting('Blog', 'rss_title', SITE_DEFAULT_TITLE, FRONTEND_LANGUAGE);
        $link = SITE_URL . FrontendNavigation::getURLForBlock('Blog');
        $description = FrontendModel::getModuleSetting('Blog', 'rss_description', null, FRONTEND_LANGUAGE);

        // create new rss instance
        $rss = new FrontendRSS($title, $link, $description);

        // loop articles
        foreach ($this->items as $item) {
            // init vars
            $title = $item['title'];
            $link = $item['full_url'];
            $description = ($item['introduction'] != '') ? $item['introduction'] : $item['text'];

            // meta is wanted
            if (FrontendModel::getModuleSetting('Blog', 'rss_meta', true, FRONTEND_LANGUAGE)) {
                // append meta
                $description .= '<div class="meta">' . "\n";
                $description .= '	<p><a href="' . $link . '" title="' . $title . '">' . $title . '</a> ' .
                                sprintf(
                                    FL::msg('WrittenBy'),
                                    FrontendUser::getBackendUser($item['user_id'])->getSetting('nickname')
                                );
                $description .= ' ' . FL::lbl('In') . ' <a href="' . $item['category_full_url'] . '" title="' .
                                $item['category_title'] . '">' . $item['category_title'] . '</a>.</p>' . "\n";

                // any tags
                if (isset($item['tags'])) {
                    // append tags-paragraph
                    $description .= '	<p>' . \SpoonFilter::ucfirst(FL::lbl('Tags')) . ': ';
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
