<?php

namespace Backend\Form\EventListener;

use Backend\Core\Engine\Model;
use Backend\Form\Type\MetaType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Use this class to add meta url generating to your symfony form.
 *
 * Example: $builder->addEventSubscriber(new AddMetaSubscriber(...));
 */
class AddMetaSubscriber implements EventSubscriberInterface
{
    /**
     * @var string - The URL shown in the backend will need this "action" to be generated.
     */
    private $actionForUrl;

    /**
     * @var string - The field in the form where the URL should be generated for.
     */
    private $baseFieldName;

    /**
     * @var string - Name of the class or the container service id, f.e.: 'moduleForUrl.repository.item',
     */
    private $generateUrlCallbackClass;

    /**
     * @var string - Name of the public method which returns you the URL, f.e.: "getUrl"
     */
    private $generateUrlCallbackMethod;

    /**
     * @var array - Extra parameters you want to include in the AJAX call to get the URL, f.e.: ["getData.getLocale", "getForm.getParent.getParent.getData.getMenuEntityId"]
     */
    private $generateUrlCallbackParameterMethods;

    /**
     * @var string - The URL shown in the backend will need this "module" to be generated.
     */
    private $moduleForUrl;

    public function __construct(
        string $moduleForUrl,
        string $actionForUrl,
        string $generateUrlCallbackClass,
        string $generateUrlCallbackMethod,
        array $generateUrlCallbackParameterMethods,
        string $baseFieldName = 'title'
    ) {
        $this->moduleForUrl = $moduleForUrl;
        $this->actionForUrl = $actionForUrl;
        $this->generateUrlCallbackClass = $generateUrlCallbackClass;
        $this->generateUrlCallbackMethod = $generateUrlCallbackMethod;
        $this->generateUrlCallbackParameterMethods = $generateUrlCallbackParameterMethods;
        $this->baseFieldName = $baseFieldName;
    }

    public static function getSubscribedEvents(): array
    {
        // Tells the dispatcher that you want to listen on the form.pre_set_data
        // event and that the preSetData method should be called.
        return array(FormEvents::PRE_SET_DATA => 'preSetData');
    }

    public function preSetData(FormEvent $event)
    {
        $event->getForm()->add(
            'meta',
            MetaType::class,
            [
                'base_field_name' => $this->baseFieldName,
                'detail_url' => Model::getUrlForBlock($this->moduleForUrl, $this->actionForUrl),
                'generate_url_callback_class' => $this->generateUrlCallbackClass,
                'generate_url_callback_method' => $this->generateUrlCallbackMethod,
                'generate_url_callback_parameters' => $this->buildCallbackParameters($event),
            ]
        );
    }

    private function buildCallbackParameters(FormEvent $event): array
    {
        $parameters = [];

        foreach ($this->generateUrlCallbackParameterMethods as $generateUrlCallbackParameterMethod) {
            $parameter = $event;
            $methods = explode('.', $generateUrlCallbackParameterMethod);

            foreach ($methods as $method) {
                $parameter = $parameter->{$method}();
            }

            $parameters[] = $parameter;
        }

        return $parameters;
    }
}
