#  Sulmi Product Bundle

Small Symfony 3 application provides basic functionality for the product, such as navigations, cataloging of products and in terms of the basic functionalities related to CRUD. Tested only in Linux.

## Requirements
---
* AsseticBundle
* StofDoctrineExtensionsBundle
* LiipImagineBundle
* KnpMenuBundle
* KnpPaginatorBundle
* DubtureFFmpegBundle
* StfalconTinymceBundle

## Installation
---

#### Using [composer](http://getcomposer.org)

Add the following lines to your ```composer.json``` in the "require-dev" and "repositories" sections:

```
# /server/dir/my-symfony-project-dir/composer.json

"require-dev": {
	(...)
	"sulmi/product-bundle": "@dev",
	(...)
}
```

```
# /server/dir/symfony-project-dir/composer.json

"repositories": [
	(...)
	{
        "type": "vcs",
        "url": "https://github.com/sulmi/product-bundle"
    },
	(...)
]
```

Run composer update command:
cd /server/dir/my-symfony-project-dir/

```
$ composer update sulmi/product-bundle

```

#### AppKernel.php

Add the following lines to your ```app/AppKernel.php``` in the ```registerBundles()``` method:

```
/**
 * {@inheritDoc}
 */
public function registerBundles() {
    (...)
    new Symfony\Bundle\AsseticBundle\AsseticBundle(),
    new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
    new Liip\ImagineBundle\LiipImagineBundle(),
    new Knp\Bundle\MenuBundle\KnpMenuBundle(),
    new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
    new Dubture\FFmpegBundle\DubtureFFmpegBundle(),
    new Stfalcon\Bundle\TinymceBundle\StfalconTinymceBundle(),
    new Sulmi\ProductBundle\SulmiProductBundle(),
    (...)
    
    if (in_array($this->getEnvironment(), array('dev', 'test'))) {
    	(...)
        $bundles[] = new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle();
        (...)
    }
}
```

## Configuration
---
#### Assetic, Security, AditionalBundles configurations

All configs a collected in files and you need to import them to default config file ```/app/config/config.yml```

Copy configuration files from directory ```vendor/sulmi/product-bundle/Sulmi/ProductBundle/Resources/config/sulmi_product*.yml``` to directory ```app/config``` and import these files to main configuration file ```config.yml```
Copy image file from directory ```vendor/sulmi/product-bundle/Sulmi/ProductBundle/Resources/data/my-logo.png``` to directory ```app/data```

Security configuration is only possible in one file. Overwrite file from project or add appropriate keys to an already imported file security.

```
# app/config/config.yml

imports:
    (...)
    - { resource: sulmi_product.yml }
    - { resource: sulmi_product_assetic.yml }
    - { resource: sulmi_product_security.yml }
    (...)
```

Make sure that all parameters are imported into the configuration file and enable you to work on database.
You may need permission to create the database.

```
# app/config/parameters.yml

parameters:
    (...)
    database_name: my_proj_db_name
    database_user: my_proj_user
    database_password: my_proj_db_pass
    (...)

```

#### Twig Configuration (forms theme and other)

Place these configurations parameters into one of the configuration files, e.g. into ```/app/config/config.yml``` file:

```
# Twig Configuration app/config/config.yml

twig:
    (...)
    paths:
        '%kernel.root_dir%/../vendor/sulmi/product-bundle/Sulmi/ProductBundle/Resources/views': SulmiProductBundle
    form_themes:
        - "bootstrap_3_layout.html.twig"
        - "SulmiProductBundle:Product:form/fields.html.twig"
    (...)
```

#### Simple Menu Language Configuration

```
# Keys language versions are connected to each other and therefore 
# if you are using record pl|en|gr under app_locales: is the key app_locales_transKeys: should look like this:
# app_locales_transKeys: Polski|English|German

# app/config/config.yml

parameters:
    (...)
    locale: pl
    app_locales: pl|en
    app_locales_transKeys: Polski|English
    (...)
(...)
# Make sure this parameter is uncommented
framework:
(...)
    translator: { fallbacks: ["%locale%"] }

(...)
```

#### Doctrine Configuration (DQL extensions)

Bundle requires proper Doctrine's mapping into one of the configuration files, e.g. into ```/app/config/config.yml``` file:

```
# Doctrine Configuration

doctrine:
	(...)
    orm:
        auto_generate_proxy_classes: %kernel.debug%
        entity_managers:
            default:
                auto_mapping: true
                filters:
                    softdeleteable:
                        class: Gedmo\SoftDeleteable\Filter\SoftDeleteableFilter
                        enabled: true
                mappings:
                    gedmo_translatable:
                        type: annotation
                        prefix: Gedmo\Translatable\Entity
                        dir: "%kernel.root_dir%/../vendor/gedmo/doctrine-extensions/lib/Gedmo/Translatable/Entity"
                        alias: GedmoTranslatable # this one is optional and will default to the name set for the mapping
                        is_bundle: false
                dql:
                    numeric_functions:
                        rand: DoctrineExtensions\Query\Mysql\Rand
                    string_functions:
                        find_in_set: DoctrineExtensions\Query\Mysql\FindInSet
	(...)
```

#### Routing configuration

Enable routing of this bundle into one of the routing files, e.g. into ```/app/config/routing.yml``` file:

```
sulmi_product_locale:
    resource: "@SulmiProductBundle/Controller/"
    type: annotation
    # Or your prefix
    prefix: /{_locale}/sulmi-product

sulmi_product_front_default:
    resource: "@SulmiProductBundle/Controller/ProductFrontEndDefaultController.php"
    type: annotation
    # Or your prefix
    prefix: /sulmi-product

_liip_imagine:
    resource: "@LiipImagineBundle/Resources/config/routing.xml"

```

#### Final commands

The final commands are necessary to ensure that all resources and schema were in place, and having the appropriate access rights.
If using Debian Linux.

```
# cd /server/dir/my-symfony-project-dir/

mkdir web/upload

sudo chmod 0775 web/upload

# In most cases, this is Apache and then command is similar to this:
# sudo chown www-data:www-data web/upload
sudo chown server-user:serwer-group web/upload

# Cache, log and session clear hard
sudo rm -r var/cache/* var/logs/* var/sessions/*

sudo chmod -R 0777 var

php bin/console d:s:v

# and if looks like need synchronisation, update schema force
php bin/console d:s:u -f

php bin/console d:f:l --append

php bin/console a:d

php bin/console assets:install web --symlink --relative

sudo chmod -R 0777 var

```

#### Simple start

To quickly get to fun enter the address like ```www.my-project-vhost.local/app_dev.php/pl/sulmi-product``` then proceed to the application home page.
After logging in, you can add categories and join them products that contain media.


Enjoy!

