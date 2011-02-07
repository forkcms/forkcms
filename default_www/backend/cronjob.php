<?php

/** require init */
require_once 'init.php';

// initialize components
new BackendInit('backend_cronjob');

// create backend-cronjob-object
new BackendCronjob();

?>