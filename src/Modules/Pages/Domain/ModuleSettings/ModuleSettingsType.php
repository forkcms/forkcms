<?php

namespace ForkCMS\Modules\Pages\Domain\ModuleSettings;

use ForkCMS\Core\Domain\Form\FieldsetType;
use ForkCMS\Core\Domain\Form\SwitchType;
use ForkCMS\Modules\Extensions\Domain\Module\Command\ChangeModuleSettings;
use ForkCMS\Modules\Pages\DependencyInjection\PagesRouteLoader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ModuleSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'navigation',
            FieldsetType::class,
            [
                'label' => 'lbl.Navigation',
                'fields' => static function (FormBuilderInterface $builder): void {
                    $builder->add(
                        'meta_navigation',
                        SwitchType::class,
                        [
                            'label' => 'lbl.MetaNavigation',
                            'help' => 'msg.HelpMetaNavigation',
                            'required' => false,
                        ]
                    );
                },
            ]
        )->add(
            'extensions',
            FieldsetType::class,
            [
                'label' => 'lbl.Extensions',
                'fields' => static function (FormBuilderInterface $builder): void {
                    $builder->add(
                        'enabled_extensions',
                        ChoiceType::class,
                        [
                            'label' => false,
                            'required' => false,
                            'choices' => explode('|', PagesRouteLoader::FORMAT_REQUIREMENT),
                            'choice_translation_domain' => false,
                            'choice_label' => static function ($value) {
                                return strtoupper($value);
                            },
                            'expanded' => true,
                            'multiple' => true,
                            'label_attr' => ['class' => 'checkbox-switch'],
                        ]
                    );
                },
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefault('data_class', ChangeModuleSettings::class);
    }
}
