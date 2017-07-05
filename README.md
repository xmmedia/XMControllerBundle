# XMControllerBundle
Provides a base for a service to help creating a filter form and record list.

## Abandoned

This package/bundle is no longer maintained. These 2 packages replace the functionality of this bundle:

  - [XMFlashBundle](https://github.com/xmmedia/XMFlashBundle)
  - [XMFormBundle](https://github.com/xmmedia/XMFormBundle)

## Installation

### Step 1: Download the Bundle

**This package is not on Packagist, so the repository will need to be added manually in composer.json**

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ php composer.phar require xm/controller-bundle
```

This command requires [Composer](https://getcomposer.org/download/).

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new XM\ControllerBundle\XMControllerBundle(),
        );

        // ...
    }
}
```
