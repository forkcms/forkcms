<?php

/** require init */
require_once 'init.php';

// initialize components
$init = new Init(APPLICATION);

/** require frontend */
require_once FRONTEND_CORE_PATH .'/engine/frontend.php';

// create website
$frontend = new Frontend();

?>