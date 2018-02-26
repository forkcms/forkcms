<?php

namespace ForkCMS\Backend\Modules\MediaGalleries\Actions;

use ForkCMS\Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use ForkCMS\Backend\Core\Language\Language;
use ForkCMS\Backend\Core\Engine\Model as BackendModel;
use ForkCMS\Backend\Modules\MediaGalleries\Domain\MediaGallery\MediaGalleryDataGrid;
use ForkCMS\Backend\Modules\MediaLibrary\Domain\MediaGroup\TypeType;

/**
 * This is the class to Show all MediaGallery Entities
 */
class MediaGalleryIndex extends BackendBaseActionIndex
{
    public function execute(): void
    {
        parent::execute();

        $form = $this->createForm(
            TypeType::class,
            null,
            [
                'action' => BackendModel::createUrlForAction('MediaGalleryAdd'),
                'method' => 'GET',
            ]
        );

        $this->template->assign('warnings', self::getWarnings());
        $this->template->assign('dataGrid', MediaGalleryDataGrid::getHtml());
        $this->template->assign('form', $form->createView());

        $this->parse();
        $this->display();
    }

    public static function getWarnings(): array
    {
        // MediaLibrary "Index" action should be allowed
        if (BackendModel::isModuleInstalled('MediaLibrary')) {
            return [];
        }

        // Add warning
        return [
            [
                'message' => sprintf(
                    Language::err('MediaLibraryModuleRequired', 'MediaGalleries'),
                    BackendModel::createUrlForAction('Modules', 'Extensions')
                ),
            ],
        ];
    }
}
