# README

## Usage

* Install the profiles module via the fork installer
* Place the profiles/layout/templates/profile.tpl in your core template, this contains the "Welcome X! Settings - Logout." stuff.
* Place the code below in src/Frontend/Core/Engine/TemplateCustom.php

```
// in the beginning of the class
use Frontend\Modules\Profiles\Engine\Model as ProfilesModel;

...

// in the parse function
ProfilesModel::parse();
```
