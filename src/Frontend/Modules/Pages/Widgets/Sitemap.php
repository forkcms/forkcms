<?php

namespace Frontend\Modules\Pages\Widgets;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;

/**
 * This is a widget wherein the sitemap lives
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Matthias Mullie <matthias.mullie@netlash.com>
 * @author Dave Lens <dave.lens@netlash.com>
 */
class Sitemap extends FrontendBaseWidget
{
    /**
     * Execute the extra
     */
    public function execute()
    {
        parent::execute();
        $this->loadTemplate();
    }
}
