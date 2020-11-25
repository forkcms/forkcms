<?php

namespace Backend\Modules\Pages\Domain\Page\Form;

use Backend\Form\Type\EditorType;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\SingleMediaGroupType;
use Backend\Modules\Pages\Domain\Page\SettingsDataTransferObject;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'metaNavigation',
                CheckboxType::class,
                [
                    'label' => 'msg.MetaNavigation',
                    'required' => false
                ]
            )
            ->add(
                'offlineTitle',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'lbl.OfflineTitle'
                ]
            )
            ->add(
                'offlineText',
                EditorType::class,
                [
                    'required' => true,
                    'label' => 'lbl.OfflineText',
                ]
            )
            ->add(
                'offlineImage',
                SingleMediaGroupType::class,
                [
                    'label' => 'lbl.Image',
                    'required' => false,
                    'minimum_items' => 0,
                ]
            );

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => SettingsDataTransferObject::class]);
    }

    public function getBlockPrefix(): string
    {
        return 'page_settings_type';
    }
}
