<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaGroup;

use Backend\Core\Engine\Header;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\Exception\MediaGroupNotFound;
use Backend\Modules\MediaLibrary\Domain\MediaItem\AspectRatio;
use Backend\Modules\MediaLibrary\Domain\MediaItem\StorageType;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Type as MediaItemPossibleType;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\Type as MediaGroupPossibleType;
use Ramsey\Uuid\Uuid;
use SimpleBus\Message\Bus\Middleware\MessageBusSupportingMiddleware;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;
use Backend\Core\Engine\Model;
use Backend\Core\Language\Language;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\Command\SaveMediaGroup;

class MediaGroupType extends AbstractType
{
    /** @var MessageBusSupportingMiddleware */
    private $commandBus;

    /** @var MediaGroupRepository */
    private $mediaGroupRepository;

    public function __construct(
        MediaGroupRepository $mediaGroupRepository,
        MessageBusSupportingMiddleware $commandBus
    ) {
        $this->mediaGroupRepository = $mediaGroupRepository;
        $this->commandBus = $commandBus;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'id',
                HiddenType::class,
                [
                    'attr' => ['class' => 'mediaGroupId'],
                ]
            )
            ->add(
                'mediaIds',
                HiddenType::class,
                [
                    'attr' => [
                        'class' => 'mediaIds',
                        'autocomplete' => 'off',
                    ],
                    'error_bubbling' => false,
                    'constraints' => $this->getConstraints($options),
                ]
            )
            ->add(
                'type',
                HiddenType::class,
                [
                    'attr' => ['class' => 'type'],
                ]
            )
            ->addModelTransformer(
                new CallbackTransformer(
                    $this->getMediaGroupTransformFunction(),
                    $this->getMediaGroupReverseTransformFunction()
                )
            );

        self::parseFiles();
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        if ($view->parent === null) {
            throw new LogicException(
                'The MediaGroupType is not a stand alone type, it needs to be used in a parent form'
            );
        }

        $view->vars['label'] = $options['label'];

        if ($options['aspect_ratio'] instanceof AspectRatio) {
            $view->vars['aspectRatio'] = $options['aspect_ratio']->asFloat();
        }
        if (\is_int($options['minimum_items'])) {
            $view->vars['minimumItems'] = $options['minimum_items'];
        }
        if (\is_int($options['maximum_items'])) {
            $view->vars['maximumItems'] = $options['maximum_items'];
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(
            [
                'label',
            ]
        );

        $resolver->setDefaults(
            [
                'label' => Language::lbl('MediaConnected'),
                'aspect_ratio' => null,
                'error_bubbling' => false,
                'minimum_items' => null,
                'maximum_items' => null,
            ]
        );

        $resolver->setAllowedTypes('aspect_ratio', ['null', AspectRatio::class]);

        $resolver->setNormalizer(
            'constraints',
            function (Options $options, $constraints = []): array {
                if (\is_int($options['minimum_items'] ?? null) || \is_int($options['maximum_items'] ?? null)) {
                    $countOptions = [];
                    if (\is_int($options['minimum_items'] ?? null)) {
                        $countOptions['min'] = $options['minimum_items'];
                        $countOptions['minMessage'] = Language::err('MinimumConnectedItems');
                    }
                    if (\is_int($options['maximum_items'] ?? null)) {
                        $countOptions['max'] = $options['maximum_items'];
                        $countOptions['maxMessage'] = Language::err('MaximumConnectedItems');
                    }

                    $constraints[] = new Constraints\Count($countOptions);
                }

                return $constraints;
            }
        );
    }

    public function getBlockPrefix(): string
    {
        return 'media_group';
    }

    private function getConstraints($options): array
    {
        if (!array_key_exists('required', $options) || !$options['required']) {
            return [];
        }

        return [
            new Constraints\NotBlank(
                [
                    'message' => Language::err('YouAreRequiredToConnectMedia', 'MediaLibrary'),
                ]
            ),
        ];
    }

    private function getMediaGroupReverseTransformFunction(): callable
    {
        return function (array $mediaGroupData): MediaGroup {
            return $this->saveMediaGroup(
                $this->getMediaGroupFromMediaGroupData($mediaGroupData),
                $this->getMediaItemIdsFromMediaGroupData($mediaGroupData)
            )->getMediaGroup();
        };
    }

    private function getMediaGroupFromMediaGroupData(array $mediaGroupData): MediaGroup
    {
        /** @var string $mediaGroupId */
        $mediaGroupId = $mediaGroupData['id'];

        try {
            /** @var MediaGroup $mediaGroup */
            $mediaGroup = $this->mediaGroupRepository->findOneById($mediaGroupId);
        } catch (MediaGroupNotFound $mediaGroupNotFound) {
            $mediaGroup = MediaGroup::createFromId(
                Uuid::fromString($mediaGroupId),
                MediaGroupPossibleType::fromString($mediaGroupData['type'])
            );
        }

        return $mediaGroup;
    }

    private function getMediaGroupTransformFunction(): callable
    {
        return static function (MediaGroup $mediaGroup) {
            return [
                'id' => $mediaGroup->getId(),
                'type' => $mediaGroup->getType(),
                'mediaIds' => implode(',', $mediaGroup->getIdsForConnectedItems()),
                'mediaGroup' => $mediaGroup,
            ];
        };
    }

    private function getMediaItemIdsFromMediaGroupData(array $mediaGroupData): array
    {
        return $mediaGroupData['mediaIds'] !== null ? explode(',', trim($mediaGroupData['mediaIds'])) : [];
    }

    public static function parseFiles(): void
    {
        // Currently Fork CMS can't load in the dependency "@header", since it is defined later when loading in
        // That's why we still use a static function to get the header
        /** @var Header $header */
        $header = Model::get('header');

        // Add "fine-uploader" css/js
        $header->addJsData('MediaLibrary', 'mediaItemTypes', MediaItemPossibleType::POSSIBLE_VALUES);
        $header->addJsData('MediaLibrary', 'mediaAllowedMovieSource', StorageType::POSSIBLE_VALUES_FOR_MOVIE);
        $header->addJsData(
            'MediaLibrary',
            'mediaAllowedExtensions',
            Model::get('media_library.manager.extension')->getAll()
        );
    }

    private function saveMediaGroup(MediaGroup $mediaGroup, array $mediaItemIds): SaveMediaGroup
    {
        /** @var SaveMediaGroup $saveMediaGroup */
        $saveMediaGroup = new SaveMediaGroup(
            $mediaGroup,
            $mediaItemIds
        );

        // Handle the MediaGroup save
        $this->commandBus->handle($saveMediaGroup);

        return $saveMediaGroup;
    }
}
