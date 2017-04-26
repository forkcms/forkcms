<?php

namespace Backend\Modules\MediaGalleries\Actions;

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Language\Language;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\MediaGalleries\Domain\MediaGallery\MediaGalleryDataGrid;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\TypeType;

/**
 * This is the class to Show all MediaGallery Entities
 */
class MediaGalleryIndex extends BackendBaseActionIndex
{
    /**
     * Execute the action
     *
     * @return void
     */
    public function execute()
    {
        parent::execute();

        $form = $this->createForm(TypeType::class, null, [
            'action' => BackendModel::createURLForAction('MediaGalleryAdd'),
            'method' => 'GET',
        ]);

        $this->tpl->assign('warnings', self::getWarnings());
        $this->tpl->assign('dataGrid', MediaGalleryDataGrid::getHtml());
        $this->tpl->assign('form', $form->createView());

        $this->parse();
        $this->display();
    }

    /**
     * Get the warnings
     *
     * @return array
     */
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
                    BackendModel::createURLForAction('Modules', 'Extensions')
                )
            ],
        ];
    }
}
