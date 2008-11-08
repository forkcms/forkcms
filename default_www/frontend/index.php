<?php
/** require init */
require_once 'init.php';

$init = new Init(APPLICATION);

/** require frontend */
require_once FRONTEND_CORE_PATH .'/engine/frontend.php';

// create website
$website = new Frontend();

?>