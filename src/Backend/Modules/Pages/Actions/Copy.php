<?php

namespace Backend\Modules\Pages\Actions;

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\Exception as BackendException;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Pages\Domain\Page\Page;
use Backend\Modules\Pages\Domain\Page\PageRepository;
use Backend\Modules\Pages\Engine\Model as BackendPagesModel;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

        // get parameters
        $from = $this->getRequest()->query->get('from');
        $to = $this->getRequest()->query->get('to');
        $pageId = $this->getRequest()->query->get('id');

        // validate
        if ($from === null) {
            throw new BackendException('Specify a from-parameter.');
        }
        if ($to === null) {
            throw new BackendException('Specify a to-parameter.');
        }

        $page = null;
        if ($pageId !== null) {
            /** @var PageRepository $pageRepository */
            $pageRepository = $this->get(PageRepository::class);
            $page = $pageRepository->findOneBy(['id' => $pageId]);

            if (!$page instanceof Page) {
                throw new NotFoundHttpException();
            }
        }

        // copy pages
        BackendPagesModel::copy($from, $to, $page);

        // redirect
        $this->redirect(BackendModel::createUrlForAction('Index') . '&report=copy-added&var=' . rawurlencode($to));
    }
}
