<?php

namespace ForkCMS\Modules\Internationalisation\Domain\Translation;

use ForkCMS\Core\Domain\Application\Application;
use ForkCMS\Modules\Extensions\Domain\Module\Module;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

/** @implements DataTransformerInterface<TranslationDomain,array> */
final class TranslationDomainType extends AbstractType implements DataTransformerInterface
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'application',
            EnumType::class,
            [
                'class' => Application::class,
                'label' => 'lbl.Application',
                'choice_label' => fn (Application $application): string =>
                    ucfirst($application->trans($this->translator)),
                'choice_filter' => static fn (?Application $application): bool =>
                    $application?->hasEditableTranslations() ?? false,
                'choice_translation_domain' => false,
            ]
        )->add(
            'module',
            EntityType::class,
            [
                'class' => Module::class,
                'choice_value' => 'name',
                'choice_label' => fn (Module $module): string =>
                    ucfirst($this->translator->trans($module->getName()->asLabel())),
                'choice_filter' => static fn (?Module $module): bool => $module?->getName() !== ModuleName::core(),
                'label' => 'lbl.Module',
                'required' => false,
                'choice_translation_domain' => false,
            ]
        )->addModelTransformer($this);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefault('label', false);
    }

    /**
     * @param TranslationDomain|null $value
     * @return array{application?:Application, module?:Module}
     */
    public function transform(mixed $value): array
    {
        if ($value instanceof TranslationDomain) {
            return [
                'application' => $value->getApplication(),
                'module' => Module::fromModuleName($value->getModuleName() ?? ModuleName::core()),
            ];
        }

        return [];
    }

    /** @param array{application?:Application, module?:Module|null} $value */
    public function reverseTransform(mixed $value): TranslationDomain
    {
        try {
            return new TranslationDomain($value['application'], $value['module']?->getName());
        } catch (Throwable $exception) {
            throw new TransformationFailedException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
