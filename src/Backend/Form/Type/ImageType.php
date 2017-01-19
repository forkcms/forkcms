<?php

namespace Backend\Form\Type;

use Common\Doctrine\ValueObject\AbstractImage;
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

class ImageType extends AbstractType
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
        if ($options['show_remove_image']) {
            $builder->add(
                'remove',
                CheckboxType::class,
                [
                    'required' => false,
                    'label' => $options['remove_image_label'],
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
                    $imageIsEmpty = ($event->getData() === null || empty($event->getData()->getFileName()));
                    $required = $imageIsEmpty && $options['required'];
                    $fileFieldOptions = [
                        'label' => false,
                        'required' => $required,
                        'attr' => ['accept' => $options['accept']],
                    ];
                    if ($required) {
                        $fileFieldOptions['constraints'] = [
                            new NotBlank(
                                ['message' => $options['required_image_error']]
                            ),
                        ];
                    }
                    $event->getForm()->add('file', SymfonyFileType::class, $fileFieldOptions);
                }
            )
            ->addModelTransformer(
                new CallbackTransformer(
                    function (AbstractImage $image = null) {
                        $this->isNew[] = $image === null;

                        return $image;
                    },
                    function ($image) use ($options) {
                        if (!$image instanceof AbstractImage && !$image instanceof stdClass) {
                            throw new TransformationFailedException('Invalid class for the image');
                        }

                        $imageClass = $options['image_class'];

                        if (!$image instanceof AbstractImage) {
                            // your editor might say the file has protected but it isn't in this case
                            $image = $imageClass::fromUploadedFile($image->file);
                        }

                        $this->nextField();

                        if ($this->isNew()) {
                            if ($this->getRemoveField() !== null && $this->getRemoveField()->getData()) {
                                return $imageClass::fromUploadedFile();
                            }

                            return clone $image;
                        }

                        if ($this->getRemoveField() !== null && $this->getRemoveField()->getData()) {
                            $image->markForDeletion();

                            return clone $image;
                        }

                        // return a clone to make sure that doctrine will do the lifecycle callbacks
                        return clone $image;
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
                'image_class',
                'show_preview',
                'show_remove_image',
                'remove_image_label',
                'required_image_error',
                'preview_image_directory',
                'accept',
            ]
        );

        $resolver->setDefaults(
            [
                'data_class' => AbstractImage::class,
                'empty_data' => function () {
                    $emptyData = new StdClass();
                    $emptyData->file = null;

                    return $emptyData;
                },
                'compound' => true,
                'preview_class' => 'img-thumbnail img-responsive',
                'show_preview' => true,
                'show_remove_image' => true,
                'required_image_error' => 'err.FieldIsRequired',
                'remove_image_label' => 'lbl.Delete',
                'preview_image_directory' => 'source',
                'accept' => 'image/*',
                'constraints' => [new Valid()],
                'error_bubbling' => false,
                'help_text_message' => 'msg.HelpImageFieldWithMaxFileSize',
                'help_text_argument' => $this->getUploadMaxFileSize(),
            ]
        );
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'fork_image';
    }

    /**
     * @return string
     */
    public function getParent()
    {
        if (!$this instanceof self) {
            return 'fork_image';
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
        $view->vars['show_remove_image'] = $options['show_remove_image'] && $form->getData() !== null
                                           && !empty($form->getData()->getFileName());
        // if you need to have an image you shouldn't be allowed to remove it
        if ($options['required']) {
            $view->vars['show_remove_image'] = false;
        }
        $imageIsEmpty = ($form->getData() === null || empty($form->getData()->getFileName()));
        $view->vars['required'] = $imageIsEmpty && $options['required'];

        $view->vars['preview_url'] = false;
        if ($form->getData() instanceof AbstractImage) {
            $view->vars['preview_url'] = $form->getData()->getWebPath($options['preview_image_directory']);
        }

        array_map(
            function ($optionName) use ($options, &$view) {
                if (array_key_exists($optionName, $options) && !empty($options[$optionName])) {
                    $view->vars[$optionName] = $options[$optionName];
                }
            },
            [
                'preview_class',
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
        ++$this->fieldOffset;
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
