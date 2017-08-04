<?php

namespace Common\Doctrine\Repository;

use Backend\Core\Engine\Exception;
use Backend\Core\Engine\Model;
use Common\Uri;
use SpoonFilter;

class MetaRepository
{
    /**
     * Generate an url, using the predefined callback.
     *
     * @param string $url The base-url to start from.
     * @param string $class The Fully Qualified Class Name or service name
     * @param string $method The method that needs to be called
     * @param array $parameters The parameters for the callback
     *
     * @throws Exception When the function does not exist
     *
     * @return string
     */
    public function generateUrl(string $url, string $class, string $method, array $parameters = []): string
    {
        // check if the class is a service
        if (Model::getContainer()->has($class)) {
            $class = Model::getContainer()->get($class);
        }

        // validate (check if the function exists)
        if (!is_callable([$class, $method])) {
            throw new Exception('The callback-method doesn\'t exist.');
        }

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
}
