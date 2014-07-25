<?php

namespace Backend\Modules\FormBuilder\Ajax;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Modules\FormBuilder\Engine\Model as BackendFormBuilderModel;

/**
 * Get a field via ajax.
 *
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class GetField extends BackendBaseAJAXAction
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        // get parameters
        $formId = trim(\SpoonFilter::getPostValue('form_id', null, '', 'int'));
        $fieldId = trim(\SpoonFilter::getPostValue('field_id', null, '', 'int'));

        // invalid form id
        if (!BackendFormBuilderModel::exists($formId)) {
            $this->output(self::BAD_REQUEST, null, 'form does not exist');
        } else {
            // invalid fieldId
            if (!BackendFormBuilderModel::existsField($fieldId, $formId)) {
                $this->output(self::BAD_REQUEST, null, 'field does not exist');
            } else {
                // get field
                $field = BackendFormBuilderModel::getField($fieldId);

                // success output
                $this->output(self::OK, array('field' => $field));
            }
        }
    }
}
