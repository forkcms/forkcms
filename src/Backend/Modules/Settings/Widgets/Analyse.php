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
 */
class Analyse extends BackendBaseWidget
{
    public function execute(): void
    {
        $this->setColumn('left');
        $this->setPosition(1);
        $this->parse();
        $this->display();
    }

    private function parse(): void
    {
        // init vars
        $warnings = BackendSettingsModel::getWarnings();

        // assign warnings
        if (!empty($warnings)) {
            $this->tpl->assign('warnings', $warnings);
        }
    }
}
