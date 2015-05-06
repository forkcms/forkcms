## SPOON_DEBUG

SPOON_DEBUG is removed. From now on you need to check if DEBUG is on by using the kernel.debug parameter, f.e.

	if ($this->getContainer()->getParameter('kernel.debug')) { ...

## SPOON_CHARSET

SPOON_CHARSET is removed. From now on you need to get the charset by using the kernel.charset parameter, f.e.

	if ($this->getContainer()->getParameter('kernel.charset')) { ...
