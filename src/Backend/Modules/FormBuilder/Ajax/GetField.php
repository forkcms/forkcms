<?php

namespace ForkCMS\Backend\Modules\FormBuilder\Ajax;

use ForkCMS\Backend\Core\Engine\Base\AjaxAction;
use ForkCMS\Backend\Modules\FormBuilder\Engine\Model as BackendFormBuilderModel;
use Symfony\Component\HttpFoundation\Response;

/**
 * Get a field via ajax.
 */
class GetField extends AjaxAction
{
    public function execute(): void
    {
        parent::execute();

        // get parameters
        $formId = trim($this->getRequest()->request->getInt('form_id'));
        $fieldId = trim($this->getRequest()->request->getInt('field_id'));

        // invalid form id
        if (!BackendFormBuilderModel::exists($formId)) {
            $this->output(Response::HTTP_BAD_REQUEST, null, 'form does not exist');

            return;
        }
        // invalid fieldId
        if (!BackendFormBuilderModel::existsField($fieldId, $formId)) {
            $this->output(Response::HTTP_BAD_REQUEST, null, 'field does not exist');

            return;
        }
        // get field
        $field = BackendFormBuilderModel::getField($fieldId);

        if ($field['type'] == 'radiobutton') {
            $values = [];

            foreach ($field['settings']['values'] as $value) {
                $values[] = $value['label'];
            }

            $field['settings']['values'] = $values;
        }

        // success output
        $this->output(Response::HTTP_OK, ['field' => $field]);
    }
}
