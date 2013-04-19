UPGRADE FROM 3.5 to 3.6
=======================

## Filesystem component

With this release we introduce the Filesystem component into Fork CMS. This
component allows us to interact with the filesystem.

More info about the Filesystem component can be found on:
http://symfony.com/doc/current/components/filesystem.html

### Upgrading your module

There are several pieces of code that should be replaced.

In the backend the filesystem component is available through the container. So
each time you need the filesystem-object in the backend you should use:

	BackendModel::getContainer()->get('filesystem');

In the frontend the filesystem component is available through the containser. 
So each time you need the filesystem-object in the backend you should use:

	FrontendModel::getContainer()->get('filesystem');

Below you can find all changes you should apply to make your module compatible
with this release. Make sure you use the correct method to retrieve the 
container.

#### SpoonFile::exists

	SpoonFile::exists(...);

Should become:

	BackendModel::getContainer()->get('filesystem')->exists(...);

#### SpoonFile::delete

	SpoonFile::delete(...);

Should become:

	BackendModel::getContainer()->get('filesystem')->remove(...);

#### SpoonFile::move

	SpoonFile::move(...);

Should become:

	BackendModel::getContainer()->get('filesystem')->rename(...);

#### SpoonDirectory::exists

	SpoonDirectory::exists(...);

Should become:

	BackendModel::getContainer()->get('filesystem')->exists(...);

#### SpoonDirectory::create

	SpoonDirectory::create(...);

Should become:

	BackendModel::getContainer()->get('filesystem')->mkdir(...);

#### SpoonFileException

	SpoonFileException(...);

Should become:

	IOException(...);

Also don't forget to add the 
`use Symfony\Component\Filesystem\Exception\IOException;`-statement at the top 
the file where you throw an IOException.


## Finder component

With this release we introduce the Finder component into Fork CMS. This
component allows us to find files and directories via an intuitive fluent
interface.

More info about the Finder component can be found on:
http://symfony.com/doc/master/components/finder.html

### Upgrading your module

