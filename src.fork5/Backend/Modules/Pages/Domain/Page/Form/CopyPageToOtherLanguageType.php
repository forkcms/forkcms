<?php

namespace Backend\Modules\Pages\Domain\Page\Form;

use Backend\Core\Engine\Model;
use Backend\Core\Language\Locale;
use Backend\Modules\Pages\Domain\Page\CopyPageDataTransferObject;
use Backend\Modules\Pages\Domain\Page\Page;
use Backend\Modules\Pages\Domain\Page\PageRepository;
use Common\ModulesSettings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CopyPageToOtherLanguageType extends AbstractType
{
    /** @var ModulesSettings */
    private $settings;

    /** @var PageRepository */
    private $pageRepository;

    public function __construct(ModulesSettings $settings, PageRepository $pageRepository)
    {
        $this->settings = $settings;
        $this->pageRepository = $pageRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $languages = $this->settings->get('Core', 'languages');
        array_splice($languages, array_search(Locale::workingLocale()->getLocale(), $languages, true), 1);
        $languageOptions = array_combine(
            array_map(
                static function (string $language) {
                    return 'lbl.' . strtoupper($language);
                },
                $languages
            ),
            $languages
        );

        $builder->add(
            'to',
            ChoiceType::class,
            [
                'choices' => $languageOptions,
                'label' => 'lbl.ToLanguage',
            ]
        );
        $builder->add('from', HiddenType::class);
        $builder->add('pageToCopy', HiddenType::class);

        $pageRepository = $this->pageRepository;
        $builder->get('pageToCopy')->addViewTransformer(
            new CallbackTransformer(
                static function (?Page $page) {
                    if (!$page instanceof Page) {
                        return null;
                    }

                    return $page->getRevisionId();
                },
                static function (?string $revisionId) use ($pageRepository) {
                    if ($revisionId === null) {
                        return null;
                    }

                    return $pageRepository->findOneBy(['revisionId' => (int) $revisionId]);
                }
            )
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'action' => Model::createUrlForAction('PageCopyToOtherLanguage'),
                'data_class' => CopyPageDataTransferObject::class,
            ]
        );
    }
}
