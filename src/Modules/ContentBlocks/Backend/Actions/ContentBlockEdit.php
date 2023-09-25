<?php

namespace ForkCMS\Modules\ContentBlocks\Backend\Actions;

use ForkCMS\Core\Domain\Header\FlashMessage\FlashMessage;
use ForkCMS\Modules\Backend\Domain\Action\AbstractFormActionController;
use ForkCMS\Modules\Backend\Domain\Action\ActionServices;
use ForkCMS\Modules\Backend\Domain\Action\ActionSlug;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\Command\ChangeContentBlock;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockRepository;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Edit an existing ContentBlock.
 */
final class ContentBlockEdit extends AbstractFormActionController
{
    public function __construct(
        ActionServices $actionServices,
        private readonly ContentBlockRepository $contentBlockRepository
    ) {
        parent::__construct($actionServices);
    }

    protected function getFormResponse(Request $request): ?Response
    {
        /** @var ContentBlock $contentBlock */
        $contentBlock = $this->getEntityFromRequest($request, ContentBlock::class);

        $this->assign('content_block', $contentBlock);
        $this->addDeleteForm(
            ['id' => $contentBlock->getRevisionId()],
            ActionSlug::fromFQCN(ContentBlockDelete::class)
        );

        $this->assign('revisions', $this->contentBlockRepository->getRevisionsForContentBlock($contentBlock));

        return $this->handleForm(
            request: $request,
            formType: ContentBlockType::class,
            formData: new ChangeContentBlock($contentBlock),
            flashMessage: FlashMessage::success('Added'),
            redirectResponse: new RedirectResponse(ContentBlockIndex::getActionSlug()->generateRoute($this->router)),
        );
    }
}
