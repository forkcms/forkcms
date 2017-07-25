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
    public function execute(): void
    {
        parent::execute();
        $this->loadTemplate();

        $this->addJS('/js/vendors/typeahead.bundle.min.js', true, false);
        $this->addCSS('Search.css');

        $form = new FrontendForm('search', FrontendNavigation::getUrlForBlock('Search'), 'get', null, false);
        $form->setParameter('class', 'navbar-form');
        $form->setParameter('role', 'search');
        $form->addText('q_widget')->setAttributes(
            [
                'itemprop' => 'query-input',
                'data-role' => 'fork-widget-search-field',
            ]
        );
        $form->parse($this->template);
    }
}
