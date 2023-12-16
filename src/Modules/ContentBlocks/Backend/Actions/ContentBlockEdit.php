<?php

namespace ForkCMS\Modules\ContentBlocks\Backend\Actions;

use ForkCMS\Core\Domain\Header\Breadcrumb\Breadcrumb;
use ForkCMS\Core\Domain\Header\FlashMessage\FlashMessage;
use ForkCMS\Modules\Backend\Domain\Action\AbstractFormActionController;
use ForkCMS\Modules\Backend\Domain\Action\ActionServices;
use ForkCMS\Modules\Backend\Domain\Action\ActionSlug;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\Command\ChangeContentBlock;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockRepository;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockType;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;
use Knp\Component\Pager\PaginatorInterface;
use Pageon\DoctrineDataGridBundle\Attribute\DataGridActionColumn;
use Pageon\DoctrineDataGridBundle\Column\Column;
use Pageon\DoctrineDataGridBundle\DataGrid\DataGrid;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Edit an existing content block.
 */
final class ContentBlockEdit extends AbstractFormActionController
{
    public function __construct(
        ActionServices $actionServices,
        private readonly ContentBlockRepository $contentBlockRepository,
        private readonly PaginatorInterface $paginator,
    ) {
        parent::__construct($actionServices);
    }

    protected function getFormResponse(Request $request): ?Response
    {
        $contentBlock = $this->getEntityFromRequest($request, ContentBlock::class);
        $this->assign('contentBlock', $contentBlock);
        $this->header->addBreadcrumb(new Breadcrumb($contentBlock->getTitle()));

        if (!$this->contentBlockRepository->isContentBlockInUse($contentBlock)) {
            $this->addDeleteForm(
                ['id' => $contentBlock->getRevisionId()],
                ActionSlug::fromFQCN(ContentBlockDelete::class)
            );
        }

        return $this->handleForm(
            request: $request,
            formType: ContentBlockType::class,
            formData: new ChangeContentBlock($contentBlock),
            redirectResponse: new RedirectResponse(ContentBlockIndex::getActionSlug()->generateRoute($this->router)),
            formOptions: ['revisions_data_grid' => $this->getRevisionDataGrid($contentBlock)],
            successFlashMessageCallback: static function (FormInterface $form): FlashMessage {
                return FlashMessage::success('Edited', ['%contentBlock%' => $form->getData()->title]);
            }
        );
    }

    private function getRevisionDataGrid(ContentBlock $contentBlock): DataGrid
    {
        return new DataGrid(
            $this->paginator->paginate($this->contentBlockRepository->getRevisionsForContentBlock($contentBlock)),
            [
                Column::createPropertyColumn(
                    name: 'title',
                    label: TranslationKey::label('Title'),
                    entityAlias: 't',
                    sortable: false,
                    filterable: false,
                    order: 1
                ),
                Column::createPropertyColumn(
                    name: 'updatedOn',
                    label: TranslationKey::label('EditedOn'),
                    entityAlias: 't',
                    sortable: false,
                    filterable: false,
                    order: 2
                ),
                Column::createPropertyColumn(
                    name: 'updatedBy',
                    label: TranslationKey::label('EditedBy'),
                    entityAlias: 't',
                    sortable: false,
                    filterable: false,
                    order: 3
                ),
                Column::createActionColumn(
                    label: TranslationKey::label('LoadRevision'),
                    order: 4,
                    route: 'backend_action',
                    routeAttributes: self::getActionSlug()->getRouteParameters(),
                    routeAttributesCallback: [ContentBlock::class, 'dataGridEditLinkCallback'],
                    class: 'btn btn-primary btn-sm',
                    iconClass: 'fa fa-edit',
                    columnAttributes: ['class' => 'fork-data-grid-action'],
                ),
            ],
            TranslationKey::message('NoRevisions')
        );
    }
}
