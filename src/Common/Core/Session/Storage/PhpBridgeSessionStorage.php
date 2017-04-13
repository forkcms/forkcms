<?php

namespace Common\Core\Session\Storage;

use SpoonSession;
use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage as SymfonyPhpBridgeSessionStorage;

/**
 * In most cases we will only access the symfony sessions after SpoonSession has started. But in the cases where that
 * is not the case the symfony sessions didn't work because of some stuff happening in SpoonSession.
 * This will fix that issue by making sure that when we start the symfony sessions the SpoonSessions also have started.
 */
final class PhpBridgeSessionStorage extends SymfonyPhpBridgeSessionStorage
{
    /**
     * {@inheritdoc}
     */
    public function start()
    {
        if ($this->started) {
            return true;
        }

        SpoonSession::start();

        return parent::start();
    }
}
