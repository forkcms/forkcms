<?php

/** require init */
require_once 'init.php';

// initialize components
new Init('backend_cronjob');

// create backend-cronjob-object
new BackendCronjob();

?>