<?php

namespace ForkCMS\Modules\Extensions\Domain\ThemeTemplate;

use ForkCMS\Core\Domain\Form\CollectionType;
use ForkCMS\Core\Domain\Form\FieldsetType;
use ForkCMS\Modules\Extensions\Domain\Theme\Theme;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ThemeTemplateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('template', FieldsetType::class, [
                'label' => TranslationKey::label('Template'),
                'fields' => static function (FormBuilderInterface $builder) {
                    $builder
                        ->add(
                            'theme',
                            EntityType::class,
                            [
                                'class' => Theme::class,
                                'required' => true,
                                'choice_label' => static function (Theme $theme) {
                                    return $theme->getName();
                                },
                                'label' => TranslationKey::label('Theme'),
                                'label_attr' => ['class' => 'visually-hidden'],
                            ]
                        )
                        ->add(
                            'path',
                            TextType::class,
                            [
                                'label' => TranslationKey::label('PathToTemplate'),
                                'help' => (string) TranslationKey::message('HelpTemplateLocation'),
                                'help_html' => true,
                            ]
                        )
                        ->add(
                            'name',
                            TextType::class,
                            [
                                'label' => TranslationKey::label('Name'),
                            ]
                        );
                },
            ])
            ->add(
                'positions',
                CollectionType::class,
                [
                    'label' => TranslationKey::label('Positions'),
                    'entry_type' => ThemeTemplatePositionType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'allow_sequence' => true,
                    'add_button_text' => TranslationKey::label('AddPosition'),
                    'delete_button_text' => TranslationKey::label('DeletePosition'),
                ]
            )
            ->add('layout', FieldsetType::class, [
                'label' => TranslationKey::label('Layout'),
                'fields' => static function (FormBuilderInterface $builder) {
                    $builder->add(
                        'layout',
                        TextareaType::class,
                        [
                            'label' => TranslationKey::label('Layout'),
                            'label_attr' => ['class' => 'visually-hidden'],
                            'help' => (string) TranslationKey::message('HelpTemplateFormat'),
                            'required' => true,
                            'attr' => [
                                'rows' => 5,
                                'cols' => 62,
                            ],
                        ]
                    );
                },
            ]);

        if ($options['show_status']) {
            $builder->add('status', FieldsetType::class, [
                'label' => TranslationKey::label('Status'),
                'fields' => static function (FormBuilderInterface $builder) use ($options) {
                    $activeOptions = [
                        'required' => false,
                        'label' => TranslationKey::label('Active'),
                    ];
                    if (!$options['can_disable']) {
                        $activeOptions['disabled'] = true;
                    }
                    $builder
                        ->add(
                            'active',
                            CheckboxType::class,
                            $activeOptions
                        )
                        ->add(
                            'default',
                            CheckboxType::class,
                            ['required' => false, 'label' => TranslationKey::label('Default')]
                        );
                },
            ]);
        }

        if ($options['show_overwrite']) {
            $builder->add('overwrite', FieldsetType::class, [
                'label' => TranslationKey::label('Overwrite'),
                'fields' => static function (FormBuilderInterface $builder) {
                    $builder->add(
                        'overwrite',
                        CheckboxType::class,
                        [
                            'required' => false,
                            'label' => TranslationKey::label('Overwrite'),
                            'help' => (string) TranslationKey::message('HelpOverwrite'),
                            'help_html' => true,
                        ]
                    );
                },
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', ThemeTemplateDataTransferObject::class);
        $resolver->setDefault('show_overwrite', false);
        $resolver->setDefault('show_status', true);
        $resolver->setDefault('can_disable', true);
    }
}
