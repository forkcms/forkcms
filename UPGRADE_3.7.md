UPGRADE FROM 3.6 to 3.7
=======================

## DependencyInjection component

Spoon Library implemented the registery pattern. This has been removed in favor of the [Dependency Injection Component](http://symfony.com/doc/current/components/dependency_injection/index.html)
of Symfony.

### Upgrading your module

* Getting

Before:
```
Spoon::get(<name>);
```

After:
```
// in actions
$this->getContainer()->get(<name>);

// in static classes (models for example)
FrontendModel::getContainer()->get(<name>);
BackendModel::getContainer()->get(<name>);
```

* Setting

Before:
```
Spoon::set(<name>, <value>);
```

After:
```
// in actions
$this->getContainer()->set(<name>, <value>);

// in static classes (models for example)
FrontendModel::getContainer()->set(<name>, <value>);
BackendModel::getContainer()->set(<name>, <value>);
```

* Checking for existance

Before:
```
Spoon::exists(<name>);
```

After:
```
// in actions
$this->getContainer()->has(<name>);

// in static classes (models for example)
FrontendModel::getContainer()->has(<name>);
BackendModel::getContainer()->has(<name>);
```
