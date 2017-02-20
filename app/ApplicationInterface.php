<?php

namespace ForkCMS\App;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Response;

/**
 * If you want to extend Fork with an application of your own, you should implement this interface
 * to ensure that initialize() and display() are *always* present.
 * /app/routing.php uses these methods to kickstart and send the output of the app to the browser.
 */
interface ApplicationInterface
{
    /**
     * This method exists because the kernel/service container needs to be set before
     * the page's functionality gets loaded. Any functionality of the app should be
     * initialized afterwards.
     */
    public function initialize();

    /**
     * Sends the output of the app to our browser, in the form of a Response object.
     *
     * @return Response
     */
    public function display();
}
