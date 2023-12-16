<?php

namespace ForkCMS\Modules\ContentBlocks\Frontend\Widgets;

use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock as ContentBlockEntity;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockRepository;
use ForkCMS\Modules\Frontend\Domain\Block\BlockServices;
use ForkCMS\Modules\Frontend\Domain\Widget\AbstractWidgetController;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentBlock extends AbstractWidgetController
{
    public function __construct(
        BlockServices $blockServices,
        private readonly ContentBlockRepository $contentBlockRepository,
    ) {
        parent::__construct($blockServices);
    }

    protected function execute(Request $request, Response $response): void
    {
        if (!$this->hasSetting('content_block_id')) {
            $this->changeTemplatePath(ContentBlockEntity::DEFAULT_TEMPLATE);

            return;
        }

        $contentBlock = $this->contentBlockRepository->findForIdAndLocale(
            $this->getSetting('content_block_id'),
            Locale::from($request->getLocale()),
        );

        if ($contentBlock === null) {
            $this->changeTemplatePath(ContentBlockEntity::DEFAULT_TEMPLATE);

            return;
        }
        $this->changeTemplatePath($contentBlock->getTemplate());
        $this->assign('content_block', $contentBlock);
    }
}
