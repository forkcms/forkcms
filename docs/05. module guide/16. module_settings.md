# Module settings

For most of the modules you'll write, you'll want to save some settings instead of saving this data hardcoded: An administrator e-mail address, the number of items to show, ... In Fork CMS there is a way to save these settings an easy way: Module settings.

Perhaps you noticed we already used a module setting in the cronjob example:

```
use Backend\Core\Engine\Model as BackendModel;

BackendModel::getModuleSetting('Core', 'admin_email', 'mail@fork-cms.com')
```

The first argument (Core) is the module, the second (admin_email) the name of the setting and the third is the default value.

The `getModuleSetting` tries to fetch the setting from the database. If it doesn't succeed, it uses the default value. Important to know is that at that moment, the default value is saved in the database. If you want to change the value, you can do so by modifying the modules_settings table, or by calling the `setModuleSetting`. This works just the same as `getModuleSetting`, except the third value is the new value.

> **Frontend settings**
> If you want to get a setting in the frontend, use FrontendModel:: instead of BackendModel::. F.e. on the index-action in the Frontend you'll find:


```
use Frontend\Core\Engine\Model as FrontendModel;

FrontendModel::getModuleSetting('MiniBlog', 'overview_num_items', 10);
```
