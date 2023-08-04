<?php

namespace ForkCMS\Modules\Installer\Domain\Locale;

use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use ForkCMS\Modules\Internationalisation\Domain\Locale\LocaleType;
use InvalidArgumentException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Builds the form to select locales to install.
 *
 * @implements DataTransformerInterface<LocalesStepConfiguration,LocalesStepConfiguration>
 */
class LocalesType extends AbstractType implements DataTransformerInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'multilingual',
            ChoiceType::class,
            [
                'expanded' => true,
                'multiple' => false,
                'choices' => [
                    'Just one locale (i.e. mysite.com/blog)' => false,
                    'Multiple locales (i.e. mysite.com/en/blog)' => true,
                ],
                'choice_attr' => static function (bool $multilingual) {
                    return [
                        'data-fork-cms-role' => $multilingual ? 'multilingual' : 'not-multilingual',
                    ];
                },
            ]
        )->add(
            'locales',
            LocaleType::class,
            [
                'expanded' => true,
                'multiple' => true,
                'choice_label' => fn (Locale $locale): string => $locale->name,
                'attr' => [
                    'data-fork-cms-role' => 'locales',
                ],
            ]
        )->add(
            'defaultLocale',
            LocaleType::class,
            [
                'label' => 'What is the default locale for your website?',
                'choice_label' => fn (Locale $locale): string => $locale->name,
                'attr' => [
                    'data-fork-cms-role' => 'default-locale',
                ],
            ]
        )->add(
            'sameInterfaceLocale',
            CheckboxType::class,
            [
                'label' => 'Use the same locale(s) for the users in the CMS interface.',
                'required' => false,
                'attr' => [
                    'data-fork-cms-role' => 'same-user-locale',
                ],
            ]
        )->add(
            'defaultUserLocale',
            LocaleType::class,
            [
                'label' => 'What is the default locale for users in the CMS interface?',
                'choice_label' => fn (Locale $locale): string => $locale->name,
                'attr' => [
                    'data-fork-cms-role' => 'default-user-locale',
                ],
            ]
        )->add(
            'userLocales',
            LocaleType::class,
            [
                'label' => 'Select the locale(s) users can choose in the CMS interface.',
                'multiple' => true,
                'choice_label' => fn (Locale $locale): string => $locale->name,
                'expanded' => true,
                'attr' => [
                    'data-fork-cms-role' => 'user-locales',
                ],
            ]
        )->addModelTransformer($this);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => LocalesStepConfiguration::class,
            ]
        );
    }

    public function getBlockPrefix(): string
    {
        return 'install_locales';
    }

    public function transform($value): LocalesStepConfiguration
    {
        return $value;
    }

    public function reverseTransform($value): LocalesStepConfiguration
    {
        if (!$value instanceof LocalesStepConfiguration) {
            throw new InvalidArgumentException('Only an instance of LocalesStepConfiguration is allowed here');
        }

        $value->normalise();

        return $value;
    }
}
