<?php

namespace Frontend\Modules\Search\Widgets;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Core\Engine\Form as FrontendForm;
use Frontend\Core\Engine\Navigation as FrontendNavigation;

/**
 * This is a widget with the search form
 */
class Form extends FrontendBaseWidget
{
    /**
     * @var FrontendForm
     */
    private $frm;

    /**
     * Execute the extra
     */
    public function execute()
    {
        parent::execute();
        $this->loadTemplate();
        $this->loadForm();
        $this->parse();
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        $this->frm = new FrontendForm('search', FrontendNavigation::getURLForBlock('Search'), 'get', null, false);
        $widgetField = $this->frm->addText('q_widget');

        $widgetField->setAttributes(
            array(
                'itemprop' => 'query-input',
                'data-role' => 'fork-widget-search-field',
            )
        );
    }

    /**
     * Parse the data into the template
     */
    private function parse()
    {
        $this->addJS('/js/vendors/typeahead.bundle.min.js', true, false);
        $this->addCSS('Search.css');
        $this->frm->parse($this->tpl);
    }
}
