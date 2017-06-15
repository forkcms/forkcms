<?php

namespace Backend\Modules\Profiles\Form;

use Backend\Core\Engine\Model;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

final class ProfileGroupDeleteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setAction(Model::createURLForAction('DeleteProfileGroup', 'Profiles'));

        $builder->add('id', HiddenType::class);
    }
}
