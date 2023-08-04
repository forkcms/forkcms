<?php

namespace Backend\Form\Type;

use Backend\Core\Engine\Header;
use Backend\Core\Engine\Model;
use Backend\Core\Language\Language;
use Common\BlockEditor\Blocks\AbstractBlock;
use Common\BlockEditor\Blocks\ButtonBlock;
use Common\BlockEditor\Blocks\HeaderBlock;
use Common\BlockEditor\Blocks\ListBlock;
use Common\BlockEditor\Blocks\MediaLibraryImageBlock;
use Common\BlockEditor\Blocks\MediaLibraryVideoBlock;
use Common\BlockEditor\Blocks\ParagraphBlock;
use Common\BlockEditor\Blocks\QuoteBlock;
use Common\BlockEditor\Blocks\RawBlock;
use Common\BlockEditor\Blocks\UnderlineBlock;
use Common\BlockEditor\EditorBlocks;
use Common\Core\Header\Priority;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class EditorType extends TextareaType
{
    /** @var ContainerInterface */
    private $container;

    /** @var string */
    private $preferredEditor;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->preferredEditor = Model::getPreferredEditor();
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($this->preferredEditor !== 'block-editor') {
            return;
        }

        $builder->addModelTransformer(
            new CallbackTransformer(
                static function (?string $json): ?string {
                    if ($json === null) {
                        return null;
                    }

                    $data = json_decode($json, true);

                    if ($data !== false
                        && is_array($data)
                        && array_key_exists('blocks', $data)
                        && array_key_exists('time', $data)) {
                        return $json;
                    }

                    return EditorBlocks::createJsonFromHtml($json);
                },
                static function (?string $json): ?string {
                    return $json;
                }
            )
        );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        switch ($this->preferredEditor) {
            case 'ck-editor':
                $this->configureCkEditorOptions($optionsResolver);

                break;
            case 'block-editor':
                $this->configureBlockEditorOptions($optionsResolver);

                break;
            default:
                parent::configureOptions($optionsResolver);

                break;
        }
    }

    public function configureBlockEditorOptions(OptionsResolver $optionsResolver): void
    {
        $editorBlocks = new EditorBlocks();
        $optionsResolver->setDefaults(
            [
                'attr' => ['class' => 'inputBlockEditor visually-hidden'],
                'blocks' => [
                    HeaderBlock::class,
                    ParagraphBlock::class,
                    ListBlock::class,
                    MediaLibraryImageBlock::class,
                    MediaLibraryVideoBlock::class,
                    QuoteBlock::class,
                    UnderlineBlock::class,
                    ButtonBlock::class,
                    RawBlock::class,
                ],
                'constraints' => [
                    new Callback(
                        [
                            'callback' => static function (
                                ?string $json,
                                ExecutionContextInterface $executionContext
                            ) use ($editorBlocks): void {
                                if ($json === null) {
                                    return;
                                }

                                if ($editorBlocks->isValid($json)) {
                                    return;
                                }

                                $executionContext->addViolation(Language::err('InvalidValue'));
                            },
                        ]
                    ),
                ],
            ]
        );

        $container = $this->container;
        $optionsResolver->setAllowedValues(
            'blocks',
            static function (array $blocks) use ($editorBlocks, $container): bool {
                if (empty($blocks)) {
                    return false;
                }

                $editorBlocks->configureBlocks(
                    ...array_map(
                        static function (string $editorBlockFCQN) use ($container): AbstractBlock {
                            return $container->get($editorBlockFCQN);
                        },
                        $blocks
                    )
                );

                return true;
            }
        );

        $optionsResolver->setDefault('editorBlocks', $editorBlocks);
        $optionsResolver->setAllowedValues(
            'editorBlocks',
            static function ($editorBlocks): bool {
                return $editorBlocks instanceof EditorBlocks;
            }
        );
    }

    public function configureCkEditorOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefault('attr', ['class' => 'inputEditor']);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $header = $this->getHeader();
        if ($header === null) {
            parent::buildView($view, $form, $options);

            return;
        }

        switch ($this->preferredEditor) {
            case 'ck-editor':
                $this->buildCkEditorView($view, $form, $options, $header);

                break;
            case 'block-editor':
                $this->buildBlockEditorView($view, $form, $options, $header);

                break;
            default:
                parent::buildView($view, $form, $options);

                break;
        }
    }

    public function buildBlockEditorView(FormView $view, FormInterface $form, array $options, Header $header): void
    {
        parent::buildView($view, $form, $options);

        $javaScriptUrls = $options['editorBlocks']->getJavaScriptUrls();
        $javaScriptUrls['/js/vendors/editor.js'] = '/js/vendors/editor.js';

        foreach ($javaScriptUrls as $url) {
            $header->addJS($url, null, true, false, true, Priority::core());
        }

        $view->vars['attr']['fork-block-editor-config'] = json_encode(
            $options['editorBlocks']->getConfig(),
            JSON_HEX_APOS
        );
    }

    public function buildCkEditorView(FormView $view, FormInterface $form, array $options, Header $header): void
    {
        parent::buildView($view, $form, $options);
        $currentLanguage = Language::getWorkingLanguage();
        // add the internal link lists-file
        if (is_file(FRONTEND_CACHE_PATH . '/Navigation/editor_link_list_' . $currentLanguage . '.js')) {
            $timestamp = @filemtime(
                FRONTEND_CACHE_PATH . '/Navigation/editor_link_list_' . $currentLanguage . '.js'
            );
            $header->addJS(
                '/src/Frontend/Cache/Navigation/editor_link_list_' . $currentLanguage . '.js?m=' . $timestamp,
                null,
                true,
                false,
                true
            );
        }
    }

    private function getHeader(): ?Header
    {
        if ($this->container->has('header')) {
            return $this->container->get('header');
        }

        return null;
    }

    public function getParent(): string
    {
        return TextareaType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'editor';
    }
}
