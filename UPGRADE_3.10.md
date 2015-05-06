## SPOON_DEBUG

SPOON_DEBUG is removed. From now on you need to check if DEBUG is on by using the kernel.debug parameter, f.e.

	if ($this->getContainer()->getParameter('kernel.debug')) { ...

## SITE_MULTILANGUAGE

SITE_MULTILANGUAGE is removed. From now on you need to check if the site is multi language by using the site.multilanguage parameter, f.e.

	if ($this->getContainer()->getParameter('site.multilanguage')) { ...