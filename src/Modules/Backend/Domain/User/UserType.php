<?php

namespace ForkCMS\Modules\Backend\Domain\User;

use ForkCMS\Core\Domain\Form\SwitchType;
use ForkCMS\Core\Domain\Form\TabsType;
use ForkCMS\Core\Domain\Settings\SettingsBag;
use ForkCMS\Modules\Backend\Domain\User\Event\BuildUserSettingsFormEvent;
use ForkCMS\Modules\Backend\Domain\UserGroup\UserGroupDataGridChoiceType;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class UserType extends AbstractType
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'user',
            TabsType::class,
            [
                'tabs' => [
                    'lbl.Authentication' => function (FormBuilderInterface $builder) use ($options): void {
                        $builder
                            ->add(
                                'displayName',
                                TextType::class,
                                [
                                    'label' => 'lbl.DisplayName',
                                    'row_attr' => ['class' => 'col-12 col-md-6 mb-3'],
                                ]
                            )
                            ->add(
                                'email',
                                EmailType::class,
                                [
                                    'label' => 'lbl.Email',
                                    'row_attr' => ['class' => 'col-12 col-md-6 mb-3'],
                                ]
                            )
                            ->add(
                                'plainTextPassword',
                                RepeatedType::class,
                                [
                                    'type' => PasswordType::class,
                                    'invalid_message' => 'err.PasswordDoesNotMatch',
                                    'first_options' => [
                                        'label' => 'lbl.Password',
                                        'row_attr' => ['class' => 'col-12 col-md-6 mb-3'],
                                        'attr' => ['autocomplete' => 'new-password'],
                                    ],
                                    'second_options' => [
                                        'label' => 'lbl.ConfirmPassword',
                                        'row_attr' => ['class' => 'col-12 col-md-6 mb-3'],
                                        'attr' => ['autocomplete' => 'new-password'],
                                    ],
                                    'required' => in_array('create', $options['validation_groups'] ?? [], true),
                                ]
                            )
                            ->add(
                                'accessToBackend',
                                SwitchType::class,
                                [
                                    'label' => 'lbl.AccessToBackend',
                                    'required' => false,
                                ]
                            );
                        /** @var User|null $user */
                        $user = $this->tokenStorage->getToken()?->getUser();
                        if ($user?->isSuperAdmin() ?? false) {
                            $builder->add(
                                'superAdmin',
                                SwitchType::class,
                                [
                                    'label' => 'lbl.SuperAdmin',
                                    'required' => false,
                                ]
                            );
                        }
                    },
                    'lbl.Groups' => static function (FormBuilderInterface $builder): void {
                        $builder->add(
                            'userGroups',
                            UserGroupDataGridChoiceType::class,
                            [
                                'required' => false,
                            ]
                        );
                    },
                    'lbl.Settings' => function (FormBuilderInterface $builder): void {
                        $settings = $builder->add(
                            'settings',
                            FormType::class,
                            [
                                'label' => false,
                                'attr' => ['class' => 'row'],
                            ]
                        )->get('settings');
                        $settings->addModelTransformer(
                            new CallbackTransformer(
                                static function (SettingsBag $settingsBag): array {
                                    return $settingsBag->all();
                                },
                                static function (array $settings): SettingsBag {
                                    return new SettingsBag($settings);
                                }
                            )
                        );
                        $this->eventDispatcher->dispatch(new BuildUserSettingsFormEvent($settings));
                    },
                ],
            ]
        );
    }
}
