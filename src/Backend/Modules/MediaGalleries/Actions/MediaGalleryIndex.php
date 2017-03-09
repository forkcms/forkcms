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
        // Call parent, this will probably add some general CSS/JS or other required files
        parent::execute();

        $this->tpl->assign('warnings', BackendMediaGalleriesModel::checkSettings());
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
        $types = Type::getPossibleValues();

        // Specially for SpoonTemplate
        $mediaGroupTypes = array();
        foreach ($types as $type) {
            $mediaGroupTypes[] = [
                'key' => $type,
                'value' => BackendModel::createURLForAction('MediaGalleryAdd') . '&type=' . $type,
                'label' => Language::lbl('MediaLibraryGroupType' . \SpoonFilter::toCamelCase($type, '-'), 'Core'),
                'selected' => ($type === 'image'),
            ];
        }

        return $mediaGroupTypes;
    }
}
