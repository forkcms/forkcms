<?php

namespace Backend\Modules\Tags\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\Action as BackendBaseAction;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Language as BL;
use Backend\Modules\Tags\Engine\Model as BackendTagsModel;

/**
 * This action is used to perform mass actions on tags (delete, ...)
 *
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Jeroen Desloovere <info@jeroendesloovere.be>
 */
class MassAction extends BackendBaseAction
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        // action to execute
        $action = \SpoonFilter::getGetValue('action', array('delete'), 'delete');

        // no id's provided
        if (!isset($_GET['id'])) {
            $this->redirect(
                BackendModel::createURLForAction('Index') . '&error=no-selection'
            );
        } else {
            // at least one id
            // redefine id's
            $ids = (array) $_GET['id'];

            // get all tags for id
            $em = BackendModel::get('doctrine.orm.entity_manager');
            $tags = $em
                ->getRepository(BackendTagsModel::ENTITY_CLASS)
                ->findBy(
                    array(
                        'id' => $ids,
                        'language' => BL::getWorkingLanguage(),
                    )
                )
            ;

            foreach ($tags as $tag) {
                // delete tag(s)
                if ($action == 'delete') {
                    BackendTagsModel::delete($tag);
                }

                // trigger event
                BackendModel::triggerEvent(
                    $this->getModule(),
                    'after_' . strtolower($action),
                    array('item' => $tag)
                );
            }
        }

        // redirect
        $this->redirect(
            BackendModel::createURLForAction('Index') . '&report=deleted'
        );
    }
}
