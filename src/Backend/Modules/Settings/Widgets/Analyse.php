<?php

namespace Backend\Modules\Settings\Widgets;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\Widget as BackendBaseWidget;
use Backend\Modules\Settings\Engine\Model as BackendSettingsModel;

/**
 * This widget will analyze the CMS warnings
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 */
class Analyse extends BackendBaseWidget
{
    /**
     * Execute the widget
     */
    public function execute()
    {
        $this->setColumn('left');
        $this->setPosition(1);
        $this->parse();
        $this->display();
    }

    /**
     * Parse into template
     */
    private function parse()
    {
        // init vars
        $warnings = BackendSettingsModel::getWarnings();

        // assign warnings
        if (!empty($warnings)) {
            $this->tpl->assign('warnings', $warnings);
        }
    }
}
