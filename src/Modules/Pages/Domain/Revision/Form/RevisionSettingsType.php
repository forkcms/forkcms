<?php

namespace ForkCMS\Modules\Pages\Domain\Revision\Form;

use ForkCMS\Core\Domain\Form\DatePickerType;
use ForkCMS\Core\Domain\Form\FieldsetType;
use ForkCMS\Core\Domain\Form\SwitchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class RevisionSettingsType extends AbstractType
{
    public function __construct(private readonly TokenStorageInterface $tokenStorage)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($this->tokenStorage->getToken()->getUser()->isSuperAdmin()) {
            $builder->add(
                'settings',
                FieldsetType::class,
                [
                    'label' => 'lbl.Settings',
                    'card' => false,
                    'fields' => static function (FormBuilderInterface $builder) use ($options): void {
                        $builder->add(
                            'navigationTitle',
                            TextType::class,
                            [
                                'label' => 'lbl.NavigationTitle',
                                'label_attr' => ['class' => 'visually-hidden'],
                                'property_path' => 'settings[navigationTitle]',
                            ]
                        );
                        $builder->add(
                            'navigationTitleOverwrite',
                            SwitchType::class,
                            [
                                'label' => 'lbl.NavigationTitle',
                                'required' => false,
                                'property_path' => 'settings[navigationTitleOverwrite]',
                            ]
                        );
                        $builder->add(
                            'linkClass',
                            TextType::class,
                            [
                                'label' => 'lbl.LinkClass',
                                'property_path' => 'settings[linkClass]',
                                'required' => false,
                            ]
                        );
                        $builder->add(
                            'allowMove',
                            SwitchType::class,
                            [
                                'label' => 'msg.Allow_move',
                                'attr' => [
                                    'data-role' => 'allow-move-toggle',
                                ],
                                'disabled' => $options['disable_allow_move'],
                                'required' => false,
                                'property_path' => 'settings[allowMove]',
                            ]
                        );
                        $builder->add(
                            'allowChildren',
                            SwitchType::class,
                            [
                                'label' => 'msg.Allow_children',
                                'attr' => [
                                    'data-role' => 'allow-children-toggle',
                                ],
                                'disabled' => $options['disable_allow_children'],
                                'required' => false,
                                'property_path' => 'settings[allowChildren]',
                            ]
                        );
                        $builder->add(
                            'allowEdit',
                            SwitchType::class,
                            [
                                'label' => 'msg.Allow_edit',
                                'attr' => [
                                    'data-role' => 'allow-edit-toggle',
                                ],
                                'required' => false,
                                'property_path' => 'settings[allowEdit]',
                            ]
                        );
                        $builder->add(
                            'allowDelete',
                            SwitchType::class,
                            [
                                'label' => 'msg.Allow_delete',
                                'attr' => [
                                    'data-role' => 'allow-delete-toggle',
                                ],
                                'disabled' => $options['disable_allow_move'],
                                'required' => false,
                                'property_path' => 'settings[allowDelete]',
                            ]
                        );
                    },
                ]
            );
        }
        $builder->add(
            'state',
            FieldsetType::class,
            [
                'label' => 'lbl.State',
                'card' => false,
                'fields' => static function (FormBuilderInterface $builder): void {
                    $builder->add(
                        'hidden',
                        ChoiceType::class,
                        [
                            'choices' => [
                                'lbl.Hidden' => true,
                                'lbl.Published' => false,
                            ],
                            'label' => false,
                            'label_attr' => [
                                'class' => 'custom-control-label radio-custom',
                            ],
                            'expanded' => true,
                            'property_path' => 'settings[hidden]',
                        ]
                    );
                    $builder->add(
                        'publishOn',
                        DatePickerType::class,
                        [
                            'label' => 'lbl.PublishOn',
                            'time' => true,
                            'property_path' => 'settings[publishOn]',
                        ]
                    );
                    $builder->add(
                        'publishUntil',
                        DatePickerType::class,
                        [
                            'label' => 'lbl.PublishTill',
                            'required' => false,
                            'time' => true,
                            'property_path' => 'settings[publishUntil]',
                        ]
                    );
                },
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'inherit_data' => true,
                'label' => false,
                'disable_allow_move' => false,
                'disable_allow_delete' => false,
                'disable_allow_children' => false,
            ]
        );
    }
}
