<?php

namespace Backend\Modules\FormBuilder\Ajax;

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Modules\FormBuilder\Engine\Model as BackendFormBuilderModel;
use Symfony\Component\HttpFoundation\Response;

/**
 * Re-sequence the fields via ajax.
 */
class Sequence extends BackendBaseAJAXAction
{
    public function execute(): void
    {
        parent::execute();

        // get parameters
        $formId = $this->getRequest()->request->getInt('form_id');
        $newIdSequence = trim($this->getRequest()->request->get('new_id_sequence', ''));

        // invalid form id
        if (!BackendFormBuilderModel::exists($formId)) {
            $this->output(Response::HTTP_BAD_REQUEST, null, 'form does not exist');

            return;
        }
        // list id
        $ids = (array) explode('|', rtrim($newIdSequence, '|'));

        // loop id's and set new sequence
        foreach ($ids as $i => $id) {
            $id = (int) $id;

            // get field
            $field = BackendFormBuilderModel::getField($id);

            // from this form and not a submit button
            if (!empty($field) && $field['form_id'] == $formId && $field['type'] != 'submit') {
                BackendFormBuilderModel::updateField($id, ['sequence' => ($i + 1)]);
            }
        }

        $this->output(Response::HTTP_OK, null, 'sequence updated');
    }
}
