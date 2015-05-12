<?php

namespace Backend\Modules\Analytics\Actions;

use Backend\Core\Engine\Base\ActionIndex;

/**
 * This is the index-action (default), it will display the overview of analytics data
 *
 * @author Wouter Sioen <wouter@sumocoders.be>
 */
class Index extends ActionIndex
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();
        $this->parse();
        $this->display();
    }
}
