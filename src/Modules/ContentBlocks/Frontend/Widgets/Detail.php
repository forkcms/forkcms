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

    protected function execute(Request $request, Response $response): void
    {
        if (!array_key_exists('extra_id', $this->assignedContent)) {
            return;
        }

        $contentBlock = $this->contentBlockRepository->findForExtraIdAndLocale(
            $this->assignedContent['extra_id'],
            $this->translator->getLocale()
        );

        if ($contentBlock === null) {
            return;
        }

        $this->assign('content_block', $contentBlock);
    }
}
