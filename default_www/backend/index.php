<?php

/** require init */
require_once 'init.php';

// initialize components
$init = new Init(APPLICATION);

/** require backend */
require_once BACKEND_CORE_PATH .'/engine/backend.php';

// create backend
$backend = new Backend();

?>