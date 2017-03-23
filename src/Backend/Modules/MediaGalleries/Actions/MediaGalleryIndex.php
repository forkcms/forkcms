<?php

namespace Backend\Modules\MediaGalleries\Actions;

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Language\Language;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\MediaGalleries\Engine\Model as BackendMediaGalleriesModel;
use Backend\Modules\MediaGalleries\Domain\MediaGallery\MediaGalleryDataGrid;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\Type;

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

        $this->tpl->assign('warnings', $this->getWarnings());
        $this->tpl->assign('dataGrid', MediaGalleryDataGrid::getHtml());
        $this->tpl->assign('mediaGroupTypes', $this->getTypes());

        $this->parse();
        $this->display();
    }

    /**
     * @return array
     */
    private function getTypes(): array
    {
        return array_map(function ($type) {
            return [
                'key' => $type,
                'value' => BackendModel::createURLForAction('MediaGalleryAdd') . '&type=' . $type,
                'label' => Language::lbl('MediaLibraryGroupType' . \SpoonFilter::toCamelCase($type, '-'), 'Core'),
                'selected' => ($type === 'image'),
            ];
        }, Type::getPossibleValues());
    }

    /**
     * Get the warnings
     *
     * @return array
     */
    public static function getWarnings(): array
    {
        // MediaLibrary "Index" action should be allowed
        if (!BackendModel::isModuleInstalled('MediaLibrary')) {
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
