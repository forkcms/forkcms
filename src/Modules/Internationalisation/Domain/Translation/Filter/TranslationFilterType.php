<?php

namespace ForkCMS\Modules\Internationalisation\Domain\Translation\Filter;

use ForkCMS\Core\Domain\Application\Application;
use ForkCMS\Modules\Extensions\Domain\Module\Module;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleRepository;
use ForkCMS\Modules\Internationalisation\Domain\Locale\InstalledLocaleType;
use ForkCMS\Modules\Internationalisation\Domain\Translation\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Contracts\Translation\TranslatorInterface;

final class TranslationFilterType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly ModuleRepository $moduleRepository
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $modules = $this->moduleRepository->findAllIndexed();
        $moduleNames = array_map(static fn (Module $module): ModuleName => $module->getName(), $modules);
        unset($moduleNames[ModuleName::core()->getName()]);
        array_unshift($moduleNames, ModuleName::core());

        $builder->add(
            'application',
            EnumType::class,
            [
                'class' => Application::class,
                'label' => 'lbl.Application',
                'choice_label' => fn (Application $application): string => ucfirst(
                    $application->trans($this->translator)
                ),
                'choice_filter' => fn (?Application $application): bool =>
                    (bool) $application?->hasEditableTranslations(),
                'choice_translation_domain' => false,
                'required' => false,
                'placeholder' => 'msg.NotFiltered',
            ]
        )->add(
            'moduleName',
            ChoiceType::class,
            [
                'choices' => $moduleNames,
                'choice_label' => fn (ModuleName $moduleName): string => ucfirst(
                    $moduleName === ModuleName::core() ? '' : $moduleName->asLabel()->trans($this->translator)
                ),
                'label' => 'lbl.Module',
                'choice_translation_domain' => false,
                'placeholder' => 'msg.NotFiltered',
                'required' => false,
            ]
        )->add(
            'type',
            EnumType::class,
            [
                'class' => Type::class,
                'label' => 'lbl.Types',
                'choice_label' => fn (Type $type): string => ucfirst($type->trans($this->translator)),
                'choice_translation_domain' => false,
                'required' => false,
                'expanded' => true,
                'multiple' => true,
            ]
        )->add(
            'locale',
            InstalledLocaleType::class,
            [
                'required' => false,
                'expanded' => true,
                'multiple' => true,
            ]
        )->add(
            'name',
            TextType::class,
            [
                'label' => 'lbl.ReferenceCode',
                'help' => 'msg.HelpReferenceCode',
                'constraints' => [
                    new Regex(pattern: '/^([a-z0-9])+$/i', message: 'err.AlphaNumericCharactersOnly'),
                ],
                'label_tooltip' => 'msg.HelpReferenceCode',
                'required' => false,
            ]
        )->add(
            'value',
            TextType::class,
            [
                'label' => 'lbl.Translation',
                'required' => false,
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('data_class', TranslationFilter::class);
    }
}
