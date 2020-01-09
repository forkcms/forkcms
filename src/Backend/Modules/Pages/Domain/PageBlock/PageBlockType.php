<?php

namespace Backend\Modules\Pages\Domain\PageBlock;

use Backend\Form\EventListener\AddMetaSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Backend\Form\Type\EditorType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

final class PageBlockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'extraType',
            ChoiceType::class,
            [
                'label' => 'lbl.Type',
                'choices' => Type::dropdownChoices(),
            ]
        );
        $builder->add(
            'visible',
            CheckboxType::class,
            [
                'label' => 'lbl.Visible',
                'required' => false,
            ]
        );
        $builder->add(
            'extraId',
            HiddenType::class,
            [
                'required' => false,
            ]
        );
        $builder->add(
            'extraData',
            HiddenType::class,
            [
                'required' => false,
            ]
        );
        $builder->add(
            'position',
            HiddenType::class,
            [
                'required' => false,
            ]
        );
        $builder->add(
            'html',
            EditorType::class,
            [
                'label' => 'lbl.Html',
                'required' => false,
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => PageBlockDataTransferObject::class]);
    }
}
