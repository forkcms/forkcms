<?php

namespace ForkCMS\Backend\Modules\Users\Actions;

use ForkCMS\Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use ForkCMS\Backend\Core\Engine\Authentication as BackendAuthentication;
use ForkCMS\Backend\Core\Engine\Model as BackendModel;
use ForkCMS\Backend\Core\Engine\User as BackendUser;
use ForkCMS\Backend\Form\Type\DeleteType;
use ForkCMS\Backend\Modules\Users\Engine\Model as BackendUsersModel;

/**
 * This is the delete-action, it will deactivate and mark the user as deleted
 */
class Delete extends BackendBaseActionDelete
{
    public function execute(): void
    {
        $deleteForm = $this->createForm(
            DeleteType::class,
            null,
            ['module' => $this->getModule()]
        );
        $deleteForm->handleRequest($this->getRequest());
        if (!$deleteForm->isSubmitted() || !$deleteForm->isValid()) {
            $this->redirect(BackendModel::createUrlForAction('Index', null, null, ['error' => 'something-went-wrong']));

            return;
        }
        $deleteFormData = $deleteForm->getData();

        $this->id = (int) $deleteFormData['id'];

        // does the user exist
        if ($this->id === 0
            || !BackendUsersModel::exists($this->id)
            || BackendAuthentication::getUser()->getUserId() === $this->id
        ) {
            $this->redirect(BackendModel::createUrlForAction('Index', null, null, ['error' => 'non-existing']));

            return;
        }

        parent::execute();

        $user = new BackendUser($this->id);

        // God-users can't be deleted
        if ($user->isGod()) {
            $this->redirect(BackendModel::createUrlForAction('Index', null, null, ['error' => 'cant-delete-god']));

            return;
        }

        BackendUsersModel::delete($this->id);

        $this->redirect(BackendModel::createUrlForAction(
            'Index',
            null,
            null,
            ['report' => 'deleted', 'var' => $user->getSetting('nickname')]
        ));
    }
}
