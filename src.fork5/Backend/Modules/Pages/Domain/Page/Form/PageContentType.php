<?php

namespace Backend\Modules\Pages\Domain\Page\Form;

use Backend\Core\Language\Language as BL;
use Backend\Modules\Extensions\Engine\Model as BackendExtensionsModel;
use Backend\Modules\Pages\Domain\ModuleExtra\ModuleExtraRepository;
use Backend\Modules\Pages\Domain\Page\Page;
use Backend\Modules\Pages\Domain\Page\PageDataTransferObject;
use Backend\Modules\Pages\Domain\PageBlock\PageBlockDataTransferObject;
use Backend\Modules\Pages\Domain\PageBlock\PageBlockType;
use Backend\Modules\Pages\Domain\PageBlock\Type;
use Common\Core\Model;
use Common\Form\CollectionType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PageContentType extends AbstractType
{
    /** @var ModuleExtraRepository */
    private $moduleExtraRepository;

    /** @var array */
    private $templates;

    public function __construct(ModuleExtraRepository $moduleExtraRepository)
    {
        $this->moduleExtraRepository = $moduleExtraRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->templates = BackendExtensionsModel::getTemplates();

        $builder->add(
            'templateId',
            ChoiceType::class,
            [
                'choices' => array_flip(
                    array_map(
                        static function (array $template): string {
                            return $template['label'];
                        },
                        $this->templates
                    )
                ),
                'choice_attr' => function (int $templateId): array {
                    return [
                        'data-config' => $this->templates[$templateId]['json'],
                    ];
                },
                'attr' => [
                    'data-role' => 'template-switcher',
                    'autocomplete' => 'off',
                ],
                'label' => 'lbl.Template',
            ]
        );

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) use ($options) : void {
                $this->buildPageBlockForm(
                    $event->getForm(),
                    $this->templates[$event->getData()['templateId']],
                    $options['load_default_blocks']
                );
            }
        );

        $this->buildPageBlockForm(
            $builder,
            $this->templates[$options['selectedTemplateId']],
            $options['load_default_blocks']
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => PageDataTransferObject::class,
                'inherit_data' => true,
                'allow_extra_fields' => true,
                'load_default_blocks' => false,
            ]
        );
        $resolver->setRequired('selectedTemplateId');
    }

    /**
     * @param FormInterface|FormBuilderInterface $form
     * @param array $selectedTemplate
     * @param bool $loadDefaultBlocks
     */
    private function buildPageBlockForm($form, array $selectedTemplate, bool $loadDefaultBlocks): void
    {
        $possibleExtraTypes = Type::dropdownChoices();

        // The home page is not allowed to have module blocks linked to it
        if (Model::getRequest()->query->getInt('id') === Page::HOME_PAGE_ID) {
            unset($possibleExtraTypes[Type::block()->getLabel()]);
        }

        if ($loadDefaultBlocks) {
            foreach ($this->getDefaultExtrasForTemplate($selectedTemplate) as $block => $defaults) {
                $pageBlocks = new ArrayCollection();

                if (empty($defaults)) {
                    continue;
                }

                foreach ($defaults as $sequence => $extraId) {
                    $pageBlocks->add($this->createPageBlockForExtraId($extraId, $block, ++$sequence));
                }

                $form->add(
                    'blocks_' . $block,
                    CollectionType::class,
                    [
                        'data' => $pageBlocks,
                        'label' => false,
                        'allow_add' => true,
                        'add_button_text' => 'lbl.AddBlock',
                        'allow_delete' => true,
                        'allow_sequence' => true,
                        'sequence_group' => 'pages',
                        'property_path' => 'blocks[' . $block . ']',
                        'entry_type' => PageBlockType::class,
                        'entry_options' => [
                            'possibleExtraTypes' => $possibleExtraTypes,
                        ],
                        'block_name' => 'page_block_collection',
                        'prototype_data' => new PageBlockDataTransferObject(),
                    ]
                );
            }
        }

        if (!isset($selectedTemplate['data']['names'])) {
            return;
        }

        // add the blocks that didn't have defaults
        foreach ($selectedTemplate['data']['names'] as $block) {
            $blockFormName = 'blocks_' . $block;
            if ($form->has($blockFormName)) {
                continue;
            }

            $form->add(
                $blockFormName,
                CollectionType::class,
                [
                    'label' => false,
                    'allow_add' => true,
                    'add_button_text' => 'lbl.AddBlock',
                    'allow_delete' => true,
                    'allow_sequence' => true,
                    'sequence_group' => 'pages',
                    'property_path' => 'blocks[' . $block . ']',
                    'entry_type' => PageBlockType::class,
                    'entry_options' => [
                        'possibleExtraTypes' => $possibleExtraTypes,
                    ],
                    'block_name' => 'page_block_collection',
                    'prototype_data' => new PageBlockDataTransferObject(),
                ]
            );
        }
    }

    private function getDefaultExtrasForTemplate(array $selectedTemplate): array
    {
        if (isset($selectedTemplate['data']['default_extras_nl'])) {
            return $selectedTemplate['data']['default_extras_nl'];
        }

        if (isset($selectedTemplate['data']['default_extras'])) {
            return $selectedTemplate['data']['default_extras'];
        }

        return [];
    }

    private function createPageBlockForExtraId(
        ?int $extraId,
        string $position,
        int $sequence
    ): PageBlockDataTransferObject {
        $pageBlock = new PageBlockDataTransferObject();
        $pageBlock->position = $position;
        $pageBlock->sequence = $sequence;

        if ($extraId === null || $extraId === 0) {
            $pageBlock->extraType = Type::richText();

            return $pageBlock;
        }

        $pageBlock->setModuleExtra($this->moduleExtraRepository->find($extraId));

        return $pageBlock;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);

        $view->vars['selectedTemplate'] = $this->templates[$options['selectedTemplateId']];
    }
}
