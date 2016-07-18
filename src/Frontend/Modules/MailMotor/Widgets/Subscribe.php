<?php

namespace Frontend\Modules\MailMotor\Widgets;

use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;

/**
 * This is a widget with the Subscribe MailMotor Action
 */
class Subscribe extends FrontendBaseWidget
{
    /**
     * Execute the extra
     *
     * @return void
     */
    public function execute()
    {
        // call parent
        parent::execute();

        // load template
        $this->loadTemplate();
    }
}
