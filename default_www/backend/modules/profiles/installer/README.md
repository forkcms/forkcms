# README

## Usage

* Install the profiles module via the fork installer
* Place the profiles/layout/templates/profile.tpl in your core template, this contains the "Welcome X! Settings - Logout." stuff.
* Place the code below in frontend/core/template_custom.php

	FrontendProfilesModel::parse();


