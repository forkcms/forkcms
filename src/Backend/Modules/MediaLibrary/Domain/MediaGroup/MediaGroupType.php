<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaGroup;

use Backend\Core\Engine\Header;
use Backend\Modules\MediaLibrary\Domain\MediaGroupMediaItem\MediaGroupMediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItemRepository;
use Backend\Modules\MediaLibrary\Domain\MediaItem\StorageType;
use Backend\Modules\MediaLibrary\Manager\ExtensionManager;
use Ramsey\Uuid\Uuid;
use SimpleBus\Message\Bus\Middleware\MessageBusSupportingMiddleware;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Backend\Core\Engine\Model;
use Backend\Core\Language\Language;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\Command\CreateMediaGroup;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\Command\SaveMediaGroup;

class MediaGroupType extends AbstractType
{
    /** @var MessageBusSupportingMiddleware */
    private $commandBus;

    /** @var MediaGroup[] */
    private $mediaGroups;

    /** @var MediaGroupRepository */
    private $mediaGroupRepository;

    /**
     * @param MediaGroupRepository $mediaGroupRepository
     * @param MessageBusSupportingMiddleware $commandBus
     */
    public function __construct(
        MediaGroupRepository $mediaGroupRepository,
        MessageBusSupportingMiddleware $commandBus
    ) {
        $this->mediaGroupRepository = $mediaGroupRepository;
        $this->commandBus = $commandBus;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
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
                    'attr' => ['class' => 'mediaIds'],
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

    public static function parseFiles()
    {
        // Currently Fork CMS can't load in the dependency "@header", since it is defined later when loading in
        // That's why we still use a static function to get the header
        /** @var Header $header */
        $header = Model::get('header');

        // Add "fine-uploader" css/js
        $header->addCSS('/css/vendors/fine-uploader/fine-uploader-new.min.css', null, true, false);
        $header->addJS('/js/vendors/jquery.fine-uploader.min.js', null, false, true);

        $header->addCSS('MediaLibrary.css', 'MediaLibrary', false, true);
        $header->addJS('MediaLibraryFolders.js', 'MediaLibrary', true);
        $header->addJS('MediaLibraryHelper.js', 'MediaLibrary', true);
        $header->addJsData('MediaLibrary', 'mediaAllowedMovieSource', StorageType::POSSIBLE_VALUES_FOR_MOVIE);
        $header->addJsData('MediaLibrary', 'mediaAllowedExtensions', Model::get('media_library.manager.extension')->getAll());
    }

    /**
     * @return \Closure
     */
    private function getMediaGroupTransformFunction()
    {
        return function (MediaGroup $mediaGroup) {
            $this->mediaGroups[(string) $mediaGroup->getId()] = $mediaGroup;

            return [
                'id' => (string) $mediaGroup->getId(),
                'type' => $mediaGroup->getType(),
                'mediaIds' => implode(',', $mediaGroup->getIdsForConnectedItems()),
            ];
        };
    }

    /**
     * @return \Closure
     */
    private function getMediaGroupReverseTransformFunction()
    {
        /**
         * @param $mediaGroupData
         * @return MediaGroup|null
         */
        return function ($mediaGroupData) {
            /** @var string $mediaGroupId */
            $mediaGroupId = $mediaGroupData['id'];

            /** @var Type $mediaGroupType */
            $mediaGroupType = Type::fromString($mediaGroupData['type']);

            /** @var array $mediaItemIds */
            $mediaItemIds = $mediaGroupData['mediaIds'] !== null
                ? explode(',', trim($mediaGroupData['mediaIds'])) : [];

            try {
                /** @var MediaGroup $mediaGroup */
                $mediaGroup = $this->mediaGroupRepository->findOneById($mediaGroupId);
            } catch (\Exception $e) {
                $mediaGroup = MediaGroup::createFromId(
                    Uuid::fromString($mediaGroupId),
                    $mediaGroupType
                );
            }

            /** @var SaveMediaGroup $updateMediaGroup */
            $updateMediaGroup = new SaveMediaGroup(
                $mediaGroup,
                $mediaItemIds
            );

            // Handle the MediaGroup update
            $this->commandBus->handle($updateMediaGroup);

            return $updateMediaGroup->getMediaGroup();
        };
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(
            [
                'label',
            ]
        );
        $resolver->setDefaults(
            [
                'label' => Language::lbl('ConnectedMedia'),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'media_group';
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ($view->parent === null) {
            throw new LogicException(
                'The MediaGroupType is not a stand alone type, it needs to be used in a parent form'
            );
        }

        $view->vars['label'] = $options['label'];
    }
}
