<?php

namespace ForkCMS\Modules\Pages\Domain\Revision\Form;

use ForkCMS\Core\Domain\Form\CollectionType;
use ForkCMS\Modules\Extensions\Domain\ThemeTemplate\ThemeTemplate;
use ForkCMS\Modules\Extensions\Domain\ThemeTemplate\ThemeTemplateRepository;
use ForkCMS\Modules\Pages\Domain\Page\Page;
use ForkCMS\Modules\Pages\Domain\Revision\RevisionDataTransferObject;
use ForkCMS\Modules\Pages\Domain\RevisionBlock\RevisionBlockDataTransferObject;
use ForkCMS\Modules\Pages\Domain\RevisionBlock\RevisionBlockType;
use ForkCMS\Modules\Pages\Domain\RevisionBlock\Type;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class RevisionContentType extends AbstractType
{
    public function __construct(
        private readonly ThemeTemplateRepository $templateTemplateRepository,
        private readonly RequestStack $requestStack
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'themeTemplate',
            ThemeTemplateType::class,
        );

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) use ($options): void {
                $data = $event->getData();
                foreach ($data as $position => $positionData) {
                    if (is_array($positionData)) {
                        array_multisort(
                            $data[$position],
                            SORT_ASC,
                            SORT_NUMERIC,
                            array_column($data[$position], 'sequence')
                        );
                    }
                }
                $this->buildrevisionBlockForm(
                    $event->getForm(),
                    $this->templateTemplateRepository->find($event->getData()['themeTemplate']),
                    $options['load_default_blocks']
                );
                $event->setData($data);
            }
        );

        $this->buildrevisionBlockForm(
            $builder,
            $options['selectedTemplate'],
            $options['load_default_blocks']
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => RevisionDataTransferObject::class,
                'inherit_data' => true,
                'allow_extra_fields' => true,
                'load_default_blocks' => false,
            ]
        );
        $resolver->setRequired('selectedTemplate');
    }

    private function buildrevisionBlockForm(
        FormInterface|FormBuilderInterface $form,
        ThemeTemplate $selectedTemplate,
        bool $loadDefaultBlocks
    ): void {
        $possibleExtraTypes = Type::formTypeChoices();

        // The home page is not allowed to have module blocks linked to it
        if ($this->requestStack->getCurrentRequest()->query->getInt('id') === Page::PAGE_ID_HOME) {
            unset($possibleExtraTypes[Type::ACTION->value]);
        }

        if ($loadDefaultBlocks) {
            $form->add(
                'blocks_1',
                TextareaType::class,
                [
                    'label' => 'lbl.Content',
                    'required' => false,
                ]
            ); // TODO: remove this
//            foreach ($this->getDefaultExtrasForTemplate($selectedTemplate) as $block => $defaults) {
//
//                $revisionBlocks = new ArrayCollection();
//
//                if (empty($defaults)) {
//                    continue;
//                }
//
//                foreach ($defaults as $sequence => $extraId) {
//                    $revisionBlocks->add($this->createrevisionBlockForExtraId($extraId, $block, ++$sequence));
//                }
//
//                $form->add(
//                    'blocks_' . $block,
//                    CollectionType::class,
//                    [
//                        'data' => $revisionBlocks,
//                        'label' => $block,
//                        'allow_add' => true,
//                        'add_button_text' => 'lbl.AddBlock',
//                        'allow_delete' => true,
//                        'allow_sequence' => true,
//                        'sequence_group' => 'pages',
//                        'property_path' => 'blocks[' . $block . ']',
//                        'entry_type' => RevisionBlockType::class,
//                        'entry_options' => [
//                            'possibleExtraTypes' => $possibleExtraTypes,
//                        ],
//                        'block_name' => 'revision_content_block',
//                        'prototype_data' => new RevisionBlockDataTransferObject(),
//                    ]
//                );
//            }
        }

        if (!$selectedTemplate->getSettings()->has('positions')) {
            return;
        }

        // add the blocks that didn't have defaults
        foreach ($selectedTemplate->getSettings()->get('positions') as $position) {
            $blockName = $position['name'];
            $blockFormName = 'blocks_' . $blockName;
            if ($form->has($blockFormName)) {
                continue;
            }

            $form->add(
                $blockFormName,
                CollectionType::class,
                [
                    'label' => $blockName,
                    'allow_add' => true,
                    'add_button_text' => 'lbl.AddBlock',
                    'allow_delete' => true,
                    'allow_sequence' => true,
                    'sequence_group' => 'pages',
                    'property_path' => 'blocks[' . $blockName . ']',
                    'entry_type' => RevisionBlockType::class,
                    'entry_options' => [
                        'possibleExtraTypes' => $possibleExtraTypes,
                    ],
                    'block_name' => 'page_block_collection',
                    'prototype_data' => new RevisionBlockDataTransferObject(),
                ]
            );
        }
    }

//    private function getDefaultExtrasForTemplate(ThemeTemplate $selectedTemplate): array
//    {
//        if (isset($selectedTemplate['data']['default_extras_nl'])) {
//            return $selectedTemplate['data']['default_extras_nl'];
//        }
//
//        if (isset($selectedTemplate['data']['default_extras'])) {
//            return $selectedTemplate['data']['default_extras'];
//        }
//
//        return [];
//    }

//    private function createrevisionBlockForExtraId(
//        ?int $extraId,
//        string $position,
//        int $sequence
//    ): RevisionBlockDataTransferObject {
//        $revisionBlock = new RevisionBlockDataTransferObject();
//        $revisionBlock->position = $position;
//        $revisionBlock->sequence = $sequence;
//
//        if ($extraId === null || $extraId === 0) {
//            return $revisionBlock;
//        }
//
//        $revisionBlock->setModuleExtra($this->moduleExtraRepository->find($extraId));
//
//        return $revisionBlock;
//    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);

        $view->vars['selectedTemplate'] = $options['selectedTemplate'];
    }
}
