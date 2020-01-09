<?php

namespace Backend\Modules\Pages\Domain\Page\Form;

use Backend\Core\Language\Language as BL;
use Backend\Modules\Extensions\Engine\Model as BackendExtensionsModel;
use Backend\Modules\Pages\Domain\ModuleExtra\ModuleExtraRepository;
use Backend\Modules\Pages\Domain\Page\PageDataTransferObject;
use Backend\Modules\Pages\Domain\PageBlock\PageBlockDataTransferObject;
use Backend\Modules\Pages\Domain\PageBlock\PageBlockType;
use Backend\Modules\Pages\Domain\PageBlock\Type;
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
            ]
        );

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event): void {
                $this->buildPageBlockForm(
                    $event->getForm(),
                    $this->templates[$event->getData()['templateId']]
                );
            }
        );

        $this->buildPageBlockForm($builder, $this->templates[$options['selectedTemplateId']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => PageDataTransferObject::class,
                'inherit_data' => true,
                'allow_extra_fields' => true,
            ]
        );
        $resolver->setRequired('selectedTemplateId');
    }

    /**
     * @param FormInterface|FormBuilderInterface $form
     * @param array $selectedTemplate
     */
    private function buildPageBlockForm($form, array $selectedTemplate): void
    {
        foreach ($this->getDefaultExtrasForTemplate($selectedTemplate) as $block => $defaults) {
            $pageBlocks = new ArrayCollection();
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
                    'allow_delete' => true,
                    'allow_sequence' => true,
                    'property_path' => 'blocks[' . $block . ']',
                    'entry_type' => PageBlockType::class,
                    'block_name' => 'page_block_collection',
                    'prototype_data' => new PageBlockDataTransferObject(),
                ]
            );
        }

        // add the blocks that didn't have defaults
        foreach ($selectedTemplate['data']['names'] ?? [] as $block) {
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
                    'allow_delete' => true,
                    'allow_sequence' => true,
                    'property_path' => 'blocks[' . $block . ']',
                    'entry_type' => PageBlockType::class,
                    'block_name' => 'page_block_collection',
                    'prototype_data' => new PageBlockDataTransferObject(),
                ]
            );
        }
    }

    private function getDefaultExtrasForTemplate(array $selectedTemplate)
    {
        return $selectedTemplate['data']['default_extras_' . BL::getWorkingLanguage()]
               ?? $selectedTemplate['data']['default_extras']
                  ?? [];
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

        // @TODO finish this
        $moduleExtra = $this->moduleExtraRepository->find($extraId);
        dump($moduleExtra);
        die;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);

        $view->vars['selectedTemplate'] = $this->templates[$options['selectedTemplateId']];
    }
}
