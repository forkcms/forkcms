<?php

namespace Backend\Modules\Pages\Domain\Page\Form;

use Backend\Modules\Extensions\Engine\Model as BackendExtensionsModel;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class TemplatesType extends ChoiceType
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['templates'] = BackendExtensionsModel::getTemplates();
        return parent::buildView($view, $form, $options);
    }
}
