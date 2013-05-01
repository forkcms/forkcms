UPGRADE FROM 3.5 to 3.6
=======================

## Filesystem component

With this release we introduce the Filesystem component into Fork CMS. This
component allows us to interact with the filesystem.

More info about the Filesystem component can be found on:
http://symfony.com/doc/current/components/filesystem.html

### Upgrading your module

There are several pieces of code that should be altered. Below you can find all
changes you should apply to make your module compatible with this release.

General rule of thumb is: use the filesystem-component if you are generating
files or are working with directories, in other cases the native PHP-methods
are prefered.


* SpoonFile::exists

   Before:
	```
	SpoonFile::exists(...)
	```

   After:
	```
	// depending on the situation you should use
	is_file(...)
	// or
	is_dir(...)
	// or
	file_exists(...)
	// or create a filesystem-object
	use Symfony\Component\Filesystem\Filesystem;
    use Symfony\Component\Filesystem\Exception\IOException;
    ...
	$fs = new Filesystem();
	$fs->exists(...)
	```

#### SpoonFile::delete

	SpoonFile::delete(...);

Should become:

	$this->getContainer()->get('filesystem')->remove(...);

#### SpoonFile::move

	SpoonFile::move(...);

Should become:

	$this->getContainer()->get('filesystem')->rename(...);

#### SpoonDirectory::exists

	SpoonDirectory::exists(...);

Should become:

	$this->getContainer()->get('filesystem')->exists(...);

* SpoonDirectory::create

   Before:
	```
	SpoonDirectory::create(...);
	```

   After:
	```
	use Symfony\Component\Filesystem\Filesystem;
    use Symfony\Component\Filesystem\Exception\IOException;
    ...
	$fs = new Filesystem();
	$fs->mkdir(...)
	```

* SpoonFileException

   Before:
	```
	SpoonFileException(...)
	```

   After:
	```
	use Symfony\Component\Filesystem\Filesystem;
    use Symfony\Component\Filesystem\Exception\IOException;
    ...
	IOException(...)
	```

## Finder component

With this release we introduce the Finder component into Fork CMS. This
component allows us to find files and directories via an intuitive fluent
interface.

More info about the Finder component can be found on:
http://symfony.com/doc/master/components/finder.html

### Upgrading your module

#### SpoonFile::getList

Wherever you need to deal with a list of files you will probably loop them.
Symfony has a nice way of iterating through the files in a path. So take the
old code below:

	$files = SpoonFile::getList($path, '/(.*).php/');
	foreach($files as $file)
	{
		Spoon::dump($file);
	}

Would become:

	$finder = new Finder();
	foreach($finder->files()->name('*.php')->in($path) as $file)
	{
		Spoon::dump($file->basename());
	}

