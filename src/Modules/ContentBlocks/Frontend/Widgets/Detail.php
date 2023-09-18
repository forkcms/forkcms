<?php

namespace ForkCMS\Modules\ContentBlocks\Frontend\Widgets;

use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockRepository;
use ForkCMS\Modules\Frontend\Domain\Block\BlockServices;
use ForkCMS\Modules\Frontend\Domain\Widget\AbstractWidgetController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Detail extends AbstractWidgetController
{
    public function __construct(
        BlockServices $blockServices,
        private readonly ContentBlockRepository $contentBlockRepository,
    ) {
        parent::__construct($blockServices);
    }

    protected function execute(Request $request, Response $respons): void
    {
        if (!$this->hasSetting('id')) {
            return;
        }

        $contentBlock = $this->contentBlockRepository->findForIdAndLocale(
            $this->getSetting('id'),
            $this->translator->getLocale()
        );

        if ($contentBlock === null) {
            return;
        }

        $this->assign('content_block', $contentBlock);
    }
}
