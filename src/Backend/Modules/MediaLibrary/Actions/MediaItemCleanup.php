<?php

namespace ForkCMS\Backend\Modules\MediaLibrary\Actions;

use ForkCMS\Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use ForkCMS\Backend\Core\Engine\Model;

class MediaItemCleanup extends BackendBaseActionIndex
{
    public function execute(): void
    {
        parent::execute();

        /** @var int $numberOfDeletedMediaItems */
        $numberOfDeletedMediaItems = $this->get('media_library.manager.item')->deleteAll();

        $this->redirect(
            $this->getBackLink(
                [
                    'report' => 'cleaned-up-media-items',
                    'var' => $numberOfDeletedMediaItems,
                ]
            )
        );
    }

    private function getBackLink(array $parameters = []): string
    {
        return Model::createUrlForAction(
            'MediaItemIndex',
            null,
            null,
            $parameters
        );
    }
}
