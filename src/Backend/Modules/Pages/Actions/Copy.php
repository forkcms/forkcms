<?php

namespace Backend\Modules\Pages\Actions;

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\Exception as BackendException;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Language\Locale;
use Backend\Modules\Pages\Domain\Page\CopyPageDataTransferObject;
use Backend\Modules\Pages\Domain\Page\Form\CopyPageType;
use Backend\Modules\Pages\Engine\Model as BackendPagesModel;
use Exception;
use ForkCMS\Utility\Module\CopyContentToOtherLocale\CopyContentFromModulesToOtherLocaleManager;

/**
 * BackendPagesCopy
 * This is the copy-action, it will copy pages from one language to another
 * Remark :    IMPORTANT existing data will be removed, this feature is also experimental!
 */
class Copy extends BackendBaseActionIndex
{
    public function execute(): void
    {
        // call parent, this will probably add some general CSS/JS or other required files
        parent::execute();

        $form = $this->createForm(CopyPageType::class, new CopyPageDataTransferObject());
        $form->handleRequest($this->getRequest());

        if (!$form->isSubmitted() || !$form->isValid()) {
            $this->redirect(BackendModel::createUrlForAction('PageIndex') . '&error=error-copy');
        }

        /** @var CopyPageDataTransferObject $data */
        $data = $form->getData();

        // validate
        if ($data->from === null) {
            throw new BackendException('Specify a from parameter.');
        }
        if ($data->to === null) {
            throw new BackendException('Specify a to parameter.');
        }

        // copy pages
        try {
            BackendModel::getContainer()
                ->get(CopyContentFromModulesToOtherLocaleManager::class)
                ->copyOne(
                    $data->pageToCopy,
                    Locale::fromString($data->from),
                    Locale::fromString($data->to)
                );
        } catch (Exception $e) {
            $this->redirect(
                BackendModel::createUrlForAction('PageEdit')
                . '&id='
                . $data->pageToCopy->getRevisionId()
                . '&error=error-copy'
            );
        }

        // redirect
        $this->redirect(
            BackendModel::createUrlForAction('PageIndex') . '&report=copy-added&var=' . rawurlencode($data->to)
        );
    }
}
