<?php

namespace Common\Form;

use Common\Core\Model;
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
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ImageType extends AbstractType
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['show_remove_image']) {
            $builder->add(
                'remove',
                CheckboxType::class,
                [
                    'required' => false,
                    'label' => $options['remove_image_label'],
                    'property_path' => 'pendingDeletion',
                ]
            );
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
                        'attr' => [
                            'accept' => $options['accept'],
                            'data-fork-cms-role' => 'image-field',
                        ],
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
                        return $image;
                    },
                    function ($image) use ($options) {
                        if (!$image instanceof AbstractImage && !$image instanceof stdClass) {
                            throw new TransformationFailedException('Invalid class for the image');
                        }

                        $imageClass = $options['image_class'];

                        if (!$image instanceof AbstractImage) {
                            $image = $imageClass::fromUploadedFile($image->getFile());
                        }

                        // return a clone to make sure that doctrine will do the lifecycle callbacks
                        return clone $image;
                    }
                )
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
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
                    return new class extends stdClass {
                        /** @var UploadedFile */
                        protected $file;

                        /** @var bool */
                        protected $pendingDeletion = false;

                        public function setFile(UploadedFile $file = null)
                        {
                            $this->file = $file;
                        }

                        public function getFile(): ?UploadedFile
                        {
                            return $this->file;
                        }

                        public function getPendingDeletion(): bool
                        {
                            return $this->pendingDeletion;
                        }

                        public function setPendingDeletion(bool $pendingDeletion)
                        {
                            $this->pendingDeletion = $pendingDeletion;
                        }
                    };
                },
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
                'help_text_argument' => function (Options $options) {
                    return $this->getUploadMaxFileSize($options['image_class']);
                },
            ]
        );
    }

    public function getBlockPrefix(): string
    {
        return 'fork_image';
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
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

    private function getMaxFileSizeServerValue(): ?int
    {
        $uploadMaxFileSize = ini_get('upload_max_filesize');

        if ($uploadMaxFileSize === false) {
            return null;
        }

        // reformat if defined as an integer
        if (is_numeric($uploadMaxFileSize)) {
            return $uploadMaxFileSize;
        }

        // reformat if specified in kB
        if (mb_strtoupper(mb_substr($uploadMaxFileSize, -1)) === 'K') {
            return mb_substr($uploadMaxFileSize, 0, -1) * 1000;
        }

        // reformat if specified in MB
        if (mb_strtoupper(mb_substr($uploadMaxFileSize, -1)) === 'M') {
            return mb_substr($uploadMaxFileSize, 0, -1) * 1000 * 1000;
        }

        // reformat if specified in GB
        if (mb_strtoupper(mb_substr($uploadMaxFileSize, -1)) === 'G') {
            return mb_substr($uploadMaxFileSize, 0, -1) * 1000 * 1000 * 1000;
        }

        return null;
    }

    private function getMaxFileSizeConstraintValue(string $imageClass): ?int
    {
        // use the metaData to find the file data
        /** @var ClassMetadata $metaData */
        $metaData = $this->validator->getMetadataFor($imageClass);
        $members = $metaData->getPropertyMetadata('file');

        // the constraints can be found in the property meta data members
        foreach ($members as $member) {
            $constraints = $member->getConstraints();

            if (count($constraints) === 1) {
                /** @var File $fileConstraint */
                $fileConstraint = $constraints[0];

                if ($fileConstraint instanceof File) {
                    return $fileConstraint->maxSize;
                }
            }
        }

        return null;
    }

    private function getUploadMaxFileSize(string $imageClass): ?string
    {
        $constraintValue = $this->getMaxFileSizeConstraintValue($imageClass);
        $serverValue = $this->getMaxFileSizeServerValue();

        if (!is_numeric($constraintValue) && !is_numeric($serverValue)) {
            return null;
        }

        // return the server value if the constraint value is to high
        if (is_numeric($constraintValue) && is_numeric($serverValue)) {
            if ($constraintValue < $serverValue) {
                return Model::prettyPrintFileSize($constraintValue);
            }

            return Model::prettyPrintFileSize($serverValue);
        }

        if (is_numeric($constraintValue)) {
            return Model::prettyPrintFileSize($constraintValue);
        }

        return Model::prettyPrintFileSize($serverValue);
    }
}
