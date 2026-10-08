<?php

namespace Common\Doctrine\Repository;

use Backend\Core\Engine\Exception;
use Common\Uri;
use Doctrine\ORM\EntityRepository;
use SpoonFilter;

class MetaRepository extends EntityRepository
{
    /**
     * The only methods that may be invoked as a meta-url callback. Every module registers its URL
     * generator through one of these (see Meta::setUrlCallback / AddMetaSubscriber).
     */
    private const ALLOWED_CALLBACK_METHODS = ['getUrl', 'getUrlForCategory'];

    /**
     * Generate an url, using the predefined callback.
     *
     * @param string $url The base-url to start from.
     * @param string $class The Fully Qualified Class Name of a module model, f.e. Backend\Modules\Blog\Engine\Model
     * @param string $method The public static method that needs to be called, only "getUrl" and
     *                       "getUrlForCategory" are supported
     * @param array $parameters The parameters for the callback
     *
     * @throws Exception When the callback is not an allowed meta-url callback
     *
     * @return string
     */
    public function generateUrl(string $url, string $class, string $method, array $parameters = []): string
    {
        // the class and method are supplied by the (authenticated) client, so never trust them: only
        // the well-known, side-effect-free module URL generators may be reached from here. This blocks
        // turning this endpoint into an arbitrary-callable / arbitrary-service primitive (CWE-470).
        $this->guardCallback($class, $method);

        // when using ->getValue() in SpoonFormText fields the function is using htmlentities(),
        // so we must decode it again first!
        $url = SpoonFilter::htmlentitiesDecode($url);

        $actualParameters = [];
        // build parameters for use in the callback
        $actualParameters[] = Uri::getUrl($url);

        // add parameters set by user
        if (!empty($parameters)) {
            foreach ($parameters as $parameter) {
                $actualParameters[] = $parameter;
            }
        }

        // get the real url
        return call_user_func_array([$class, $method], $actualParameters);
    }

    /**
     * Make sure the requested callback is one of the allowed, side-effect-free module URL generators.
     *
     * @throws Exception When the class/method pair is not an allowed meta-url callback
     */
    private function guardCallback(string $class, string $method): void
    {
        if (!in_array($method, self::ALLOWED_CALLBACK_METHODS, true)) {
            throw new Exception('The callback-method doesn\'t exist.');
        }

        // every meta-url callback lives on a module model: Backend\Modules\<Module>\Engine\Model
        // (Frontend mirrors the same convention). Nothing else may be instantiated or resolved here.
        $isModuleModel = (
            str_starts_with($class, 'Backend\\Modules\\')
            || str_starts_with($class, 'Frontend\\Modules\\')
        ) && str_ends_with($class, '\\Engine\\Model');

        if (!$isModuleModel || !class_exists($class)) {
            throw new Exception('The callback-method doesn\'t exist.');
        }

        // the callback is always a public static method; reject instance-only or non-existing methods
        if (!method_exists($class, $method) || !(new \ReflectionMethod($class, $method))->isStatic()) {
            throw new Exception('The callback-method doesn\'t exist.');
        }
    }
}
