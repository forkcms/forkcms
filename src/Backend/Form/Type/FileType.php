<?php

namespace Backend\Form\Type;

use Common\Doctrine\ValueObject\AbstractFile;
use stdClass;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType as SymfonyFileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

class FileType extends AbstractType
{
    /** @var CheckboxType|null */
    private $removeFields;

    /** @var int in collections we need multiple references, this helps us keep track of the current one */
    private $fieldOffset = -1;

    /** @var array */
    private $isNew = [];

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['show_remove_file']) {
            $builder->add(
                'remove',
                CheckboxType::class,
                [
                    'required' => false,
                    'label' => $options['remove_file_label'],
                    'mapped' => false,
                ]
            );

            $this->removeFields[] = $builder->get('remove');
        } else {
            $this->removeFields[] = null;
        }

        $builder
            ->addEventListener(
                FormEvents::PRE_SET_DATA,
                function (FormEvent $event) use ($options) {
                    $fileIsEmpty = ($event->getData() === null || empty($event->getData()->getFileName()));
                    $required = $fileIsEmpty && $options['required'];
                    $fileFieldOptions = [
                        'label' => false,
                        'required' => $required,
                        'attr' => ['accept' => $options['accept']],
                    ];
                    if ($required) {
                        $fileFieldOptions['constraints'] = [
                            new NotBlank(
                                ['message' => $options['required_file_error']]
                            ),
                        ];
                    }
                    $event->getForm()->add('file', SymfonyFileType::class, $fileFieldOptions);
                }
            )
            ->addModelTransformer(
                new CallbackTransformer(
                    function (AbstractFile $file = null) {
                        $this->isNew[] = $file === null;

                        return $file;
                    },
                    function ($file) use ($options) {
                        if (!$file instanceof AbstractFile && !$file instanceof stdClass) {
                            throw new TransformationFailedException('Invalid class for the file');
                        }

                        $fileClass = $options['file_class'];

                        if (!$file instanceof AbstractFile) {
                            // your editor might say the file has protected but it isn't in this case
                            $file = $fileClass::fromUploadedFile($file->file);
                        }

                        $this->nextField();

                        if ($this->isNew()) {
                            if ($this->getRemoveField() !== null && $this->getRemoveField()->getData()) {
                                return $fileClass::fromUploadedFile();
                            }

                            return clone $file;
                        }

                        if ($this->getRemoveField() !== null && $this->getRemoveField()->getData()) {
                            $file->markForDeletion();

                            return clone $file;
                        }

                        // return a clone to make sure that doctrine will do the lifecycle callbacks
                        return clone $file;
                    }
                )
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(
            [
                'file_class',
                'show_preview',
                'preview_label',
                'show_remove_file',
                'remove_file_label',
                'required_file_error',
            ]
        );

        $resolver->setDefaults(
            [
                'data_class' => AbstractFile::class,
                'empty_data' => function () {
                    $emptyData = new StdClass();
                    $emptyData->file = null;

                    return $emptyData;
                },
                'compound' => true,
                'preview_label' => 'lbl.ViewCurrentFile',
                'show_preview' => true,
                'show_remove_file' => true,
                'required_file_error' => 'err.FieldIsRequired',
                'remove_file_label' => 'lbl.Delete',
                'accept' => null,
                'constraints' => array(new Valid()),
                'error_bubbling' => false,
                'help_text_message' => 'msg.HelpMaxFileSize',
                'help_text_argument' => $this->getUploadMaxFileSize(),
            ]
        );
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'fork_file';
    }

    /**
     * @return string
     */
    public function getParent()
    {
        if (!$this instanceof self) {
            return $this->getBlockPrefix();
        }

        return 'file';
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['show_preview'] = $options['show_preview'];
        $view->vars['show_remove_file'] = $options['show_remove_file'] && $form->getData() !== null
                                          && !empty($form->getData()->getFileName());
        // if you need to have an file you shouldn't be allowed to remove it
        if ($options['required']) {
            $view->vars['show_remove_file'] = false;
        }
        $imageIsEmpty = ($form->getData() === null || empty($form->getData()->getFileName()));
        $view->vars['required'] = $imageIsEmpty && $options['required'];

        $view->vars['preview_url'] = false;
        if ($form->getData() instanceof AbstractFile) {
            $view->vars['preview_url'] = $form->getData()->getWebPath();
        }
        array_map(
            function ($optionName) use ($options, &$view) {
                if (array_key_exists($optionName, $options) && !empty($options[$optionName])) {
                    $view->vars[$optionName] = $options[$optionName];
                }
            },
            [
                'preview_label',
                'help_text_message',
                'help_text_argument',
            ]
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * Increases the current index
     */
    private function nextField()
    {
        $this->fieldOffset++;
    }

    /**
     * @return CheckboxType
     */
    private function getRemoveField()
    {
        return $this->removeFields[$this->fieldOffset];
    }

    /**
     * @return bool
     */
    private function isNew()
    {
        return $this->isNew[$this->fieldOffset];
    }

    /**
     * @return string|null
     */
    private function getUploadMaxFileSize()
    {
        $uploadMaxFileSize = ini_get('upload_max_filesize');
        if ($uploadMaxFileSize === false) {
            return;
        }

        // reformat if defined as an integer
        if (is_numeric($uploadMaxFileSize)) {
            return $uploadMaxFileSize / 1024 . 'MB';
        }

        // reformat if specified in kB
        if (mb_strtoupper(mb_substr($uploadMaxFileSize, -1)) === 'K') {
            return mb_substr($uploadMaxFileSize, 0, -1) . 'kB';
        }

        // reformat if specified in MB
        if (mb_strtoupper(mb_substr($uploadMaxFileSize, -1)) === 'M') {
            return $uploadMaxFileSize . 'B';
        }

        // reformat if specified in GB
        if (mb_strtoupper(mb_substr($uploadMaxFileSize, -1)) === 'G') {
            return $uploadMaxFileSize . 'B';
        }

        return $uploadMaxFileSize;
    }
}
