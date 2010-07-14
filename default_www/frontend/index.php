<?php

// if someone tries to access the folder frontend, we redirect them to the homepage
if(!defined('APPLICATION'))
{
	// redirect
	header('Location: /');

	// stop script
	exit;
}

// require init
require_once 'init.php';

// initialize components
new Init(APPLICATION);

// create frontend
new Frontend();

?>