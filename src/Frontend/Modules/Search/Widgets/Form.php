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
 *
 * @author Matthias Mullie <forkcms@mullie.eu>
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
        $this->frm->addText('q_widget', null, 255, 'inputText autoSuggest', 'inputTextError autoSuggest');
    }

    /**
     * Parse the data into the template
     */
    private function parse()
    {
        $this->frm->parse($this->tpl);
    }
}
