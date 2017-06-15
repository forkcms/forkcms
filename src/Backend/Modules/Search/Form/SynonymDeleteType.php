<?php

namespace Backend\Modules\Search\Form;

use Backend\Core\Engine\Model;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

final class SynonymDeleteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setAction(Model::createURLForAction('DeleteSynonym', 'Search'));

        $builder->add('id', HiddenType::class);
    }
}
