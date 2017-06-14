<?php

namespace Backend\Modules\Extensions\Form;

use Backend\Core\Engine\Model;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

final class ThemeTemplateDeleteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setAction(Model::createURLForAction('DeleteThemeTemplate', 'Extensions'));

        $builder->add('id', HiddenType::class);
    }
}
