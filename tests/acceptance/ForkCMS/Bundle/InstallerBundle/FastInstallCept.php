<?php

use Codeception\Scenario as Scenario;

/** $scenario \Codeception\Scenario  */
$scenario->group('install');

$I = new WebGuy($scenario);
$I->wantTo('Install Fork CMS');

# Step 1
$I->amOnPage('/');
$I->see('Install Fork CMS');
$I->see('Languages');
$I->click('Next');

# Step 2
$I->see('Modules');
$I->uncheckOption('install_modules_example_data');
$I->click('Next');

# Step 3
$I->see('Database');

