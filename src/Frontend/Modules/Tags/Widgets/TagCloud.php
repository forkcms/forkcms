<?php

namespace ForkCMS\Frontend\Modules\Tags\Widgets;

use ForkCMS\Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use ForkCMS\Frontend\Core\Engine\Navigation as FrontendNavigation;
use ForkCMS\Frontend\Modules\Tags\Engine\Model as FrontendTagsModel;

class TagCloud extends FrontendBaseWidget
{
    public function execute(): void
    {
        parent::execute();
        $this->loadTemplate();
        $this->parse();
    }

    private function parse(): void
    {
        $tags = FrontendTagsModel::getMostUsed(10);

        if (empty($tags)) {
            $this->template->assign('widgetTagsTagCloud', []);

            return;
        }

        $link = FrontendNavigation::getUrlForBlock($this->getModule(), 'Detail');

        $this->template->assign(
            'widgetTagsTagCloud',
            array_map(
                function (array $tag) use ($link) {
                    $tag['url'] = $link . '/' . $tag['url'];

                    return $tag;
                },
                $tags
            )
        );
    }
}
