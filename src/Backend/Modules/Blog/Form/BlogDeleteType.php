<?php

namespace Backend\Modules\Blog\Form;

use App\Form\Type\Backend\DeleteType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

final class BlogDeleteType extends DeleteType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->add('categoryId', HiddenType::class);
    }
}
