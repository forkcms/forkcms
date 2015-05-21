## SPOON_DEBUG

SPOON_DEBUG is removed. From now on you need to check if DEBUG is on by using the kernel.debug parameter, f.e.

	if ($this->getContainer()->getParameter('kernel.debug')) { ...

## SITE_MULTILANGUAGE

SITE_MULTILANGUAGE is removed. From now on you need to check if the site is multi language by using the site.multilanguage parameter, f.e.

	if ($this->getContainer()->getParameter('site.multilanguage')) { ...

## SPOON_DEBUG_EMAIL

SPOON_DEBUG_EMAIL is removed. From now on you need to get the debug email address by using the fork.debug_email parameter, f.e.

	if ($this->getContainer()->getParameter('fork.debug_email')) { ...

## SPOON_CHARSET

SPOON_CHARSET is removed. From now on you need to get the charset by using the kernel.charset parameter, f.e.

	if ($this->getContainer()->getParameter('kernel.charset')) { ...

## Module settings

The getModuleSetting, getModuleSettings, deleteModuleSetting and setModuleSettings on the Frontend and BackendModel are now deprecated. You should use the fork.settings service. You can use it this way:

    $this->get('fork.settings')->set('Core', 'Theme', 'triton');
    $this->get('fork.settings')->get('Core', 'Theme');
    $this->get('fork.settings')->getForModule('Core');
    $this->get('fork.settings')->delete('Core', 'Theme');

This makes sure the modulesettings are more decoupled. They are now fully unit tested. We're also sure the Frontend and backend are consistent now and there is only one DB call to fetch all settings.
