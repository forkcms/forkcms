<?php

namespace Backend\Modules\Pages\Domain\Page\Form;

use Backend\Core\Language\Language as BL;
use Backend\Core\Language\Locale;
use Backend\Modules\Pages\Engine\Model as BackendPagesModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PageDataHreflangType extends AbstractType implements PageDataTypeInterface
{
    /** @var bool */
    private static $siteIsMultiLingual = false;

    public function __construct(bool $siteIsMultiLingual)
    {
        self::$siteIsMultiLingual = $siteIsMultiLingual;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!self::$siteIsMultiLingual) {
            return;
        }

        foreach (BL::getActiveLanguages() as $language) {
            // we don't want the current language because this is a redirect to a different language selector
            if ($language === BL::getWorkingLanguage()) {
                continue;
            }

            $builder->add(
                'hreflang_' . $language . '',
                ChoiceType::class,
                [
                    'choices' => array_flip(BackendPagesModel::getPagesForDropdown(Locale::fromString($language))),
                    'label' => 'lbl.' . mb_strtoupper($language),
                    'required' => false,
                ]
            );
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'inherit_data' => true,
                'label' => false,
            ]
        );
    }

    public static function transform(?array $persistedData, array $transformedData): array
    {
        if (!self::$siteIsMultiLingual || $persistedData === null) {
            return $transformedData;
        }

        return $transformedData;
    }

    public static function reverseTransform(array $submittedData, array $transformedData): array
    {
        if (!self::$siteIsMultiLingual) {
            return $transformedData;
        }

        foreach (BL::getActiveLanguages() as $language) {
            if ($language !== BL::getWorkingLanguage()) {
                $transformedData['hreflang_' . $language] = $submittedData['hreflang_' . $language] ?? null;
            }
        }

        return $transformedData;
    }
}
