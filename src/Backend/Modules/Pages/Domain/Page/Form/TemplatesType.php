<?php

namespace Backend\Modules\Pages\Domain\Page\Form;

use Backend\Modules\Extensions\Engine\Model as BackendExtensionsModel;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TemplatesType extends ChoiceType
{
    /** @var array */
    private $templates;

    public function __construct()
    {
        parent::__construct(null);
        $this->templates = BackendExtensionsModel::getTemplates();
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['templates'] = $this->templates;
        return parent::buildView($view, $form, $options);
    }
}
