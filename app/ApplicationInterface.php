<?php

namespace ForkCMS\App;

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
    public function initialize(): void;

    /**
     * Sends the output of the app to our browser, in the form of a Response object.
     */
    public function display(): Response;

    /**
     * This is fairly dirty, but so is having static method classes for models.
     * Consider this a temporary solution until we have genuine models available.
     */
    public function passContainerToModels(): void;
}
