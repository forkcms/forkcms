<?php

namespace ForkCMS\Modules\Internationalisation\Domain\Twig;

use DateTimeImmutable;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleSettings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

final class FormatSettingsType extends AbstractType
{
    public function __construct(
        private readonly ModuleSettings $moduleSettings,
        private readonly ForkIntlExtension $intlExtension,
        private readonly Environment $twig,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $previewDate = DateTimeImmutable::createFromFormat('Y/m/d H:i:s', '1991/03/24 02:50:01');
        $previewLongDate = DateTimeImmutable::createFromFormat('Y/m/d H:i:s', '1989/11/09 18:53:01');
        $coreModule = ModuleName::core();

        $builder->add(
            'date_format_short',
            ChoiceType::class,
            [
                'label' => 'lbl.DateFormatShort',
                'help' => 'msg.HelpDateFormatShort',
                'choices' => array_flip($this->moduleSettings->get($coreModule, 'date_formats_short')),
                'choice_label' => function ($value, $key) use ($previewDate): string {
                    return $this->intlExtension->formatDate($this->twig, $previewDate, null, $key);
                },
                'row_attr' => ['class' => 'col-12 col-md-6 mb-3'],
                'choice_translation_domain' => false,
            ]
        )->add(
            'date_format_long',
            ChoiceType::class,
            [
                'label' => 'lbl.DateFormatLong',
                'help' => 'msg.HelpDateFormatLong',
                'choices' => array_flip($this->moduleSettings->get($coreModule, 'date_formats_long')),
                'choice_label' => function ($value, $key) use ($previewLongDate): string {
                    return $this->intlExtension->formatDate($this->twig, $previewLongDate, null, $key);
                },
                'row_attr' => ['class' => 'col-12 col-md-6 mb-3'],
                'choice_translation_domain' => false,
            ]
        )->add(
            'time_format',
            ChoiceType::class,
            [
                'label' => 'lbl.TimeFormat',
                'choices' => array_flip($this->moduleSettings->get($coreModule, 'time_formats')),
                'choice_label' => function ($value, $key) use ($previewDate): string {
                    return $this->intlExtension->formatDate($this->twig, $previewDate, null, $key);
                },
                'row_attr' => ['class' => 'col-12 col-md-6 mb-3'],
                'choice_translation_domain' => false,
            ]
        )->add(
            'date_time_order',
            ChoiceType::class,
            [
                'label' => 'lbl.DateTimeOrder',
                'choices' => $this->moduleSettings->get($coreModule, 'date_time_orders'),
                'row_attr' => ['class' => 'col-12 col-md-6 mb-3'],
                'choice_translation_domain' => false,
            ]
        )->add(
            'number_format',
            ChoiceType::class,
            [
                'label' => 'lbl.NumberFormat',
                'choices' => $this->moduleSettings->get($coreModule, 'number_formats'),
                'row_attr' => ['class' => 'col-12 col-md-6 mb-3'],
                'choice_translation_domain' => false,
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefault('attr', ['class' => 'row']);
    }
}
