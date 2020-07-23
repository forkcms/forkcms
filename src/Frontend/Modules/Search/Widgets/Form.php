<?php

namespace Frontend\Modules\Search\Widgets;

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

        $form = new FrontendForm('search', FrontendNavigation::getUrlForBlock('Search'), 'get', null, false);
        $form->setParameter('class', 'form-inline my-2 my-lg-0 ml-2');
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
