<?php

namespace Backend\Modules\MediaLibrary\Actions;

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\Model;

class MediaItemCleanup extends BackendBaseActionIndex
{
    public function execute()
    {
        parent::execute();

        /** @var int $numberOfDeletedMediaItems */
        $numberOfDeletedMediaItems = $this->get('media_library.manager.item')->deleteAll();

        return $this->redirect(
            $this->getBackLink(
                [
                    'report' => 'cleaned-up-media-items',
                    'var' => $numberOfDeletedMediaItems,
                ]
            )
        );
    }

    /**
     * @param array $parameters
     *
     * @return string
     */
    private function getBackLink(array $parameters = []): string
    {
        return Model::createURLForAction(
            'MediaItemIndex',
            null,
            null,
            $parameters
        );
    }
}
