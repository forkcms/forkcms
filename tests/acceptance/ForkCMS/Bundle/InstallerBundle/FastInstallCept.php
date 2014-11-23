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
$I->see('Enter your database details');
$I->fillField('install_database_dbDatabase','fork_cms_test');
$I->fillField('install_database_dbUsername','root');
$I->fillField('install_database_dbPassword','pa$$word');
$I->click('Next');

# Step 4
$I->see('Enter the e-mail address ');
$I->fillField('install_login_email','tests@forkcms.org');
$I->fillField('install_login_password_first','te$ts');
$I->fillField('install_login_password_second','te$ts');
$I->click('Finish installation');
