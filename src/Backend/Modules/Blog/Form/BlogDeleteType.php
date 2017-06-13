<?php

namespace Backend\Modules\Blog\Form;

use Backend\Core\Engine\Model;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

final class BlogDeleteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setAction(Model::createURLForAction('Delete', 'Blog'));

        $builder->add('id', HiddenType::class);
        $builder->add('categoryId', HiddenType::class);
    }
}
