Installation
============

Set up the SonataUserBundle
---------------------------

Add the bundle to your project:

    git://github.com/sonata-project/SonataUserBundle.git

    // dependency bundle
    git://github.com/sonata-project/SonataEasyExtendsBundle.git
    git://github.com/sonata-project/SonataAdminBundle.git

Adjust your autoload.php so they are found.

In your AppKernel you enable the bundles:

.. code-block:: php

    <?php
    // app/appkernel.php
    public function registerbundles()
    {
        return array(
            // ...
            // You have 2 options to initialize the SonataUserBundle in your AppKernel,
            // you can select which bundle SonataUserBundle extends
            // extend the ``FOSUserBundle``
            new Sonata\UserBundle\SonataUserBundle('FOSUserBundle');
            // OR
            // the bundle will NOT extend ``FOSUserBundle``
            new Sonata\UserBundle\SonataUserBundle();

            new Sonata\AdminBundle\SonataAdminBundle(),
            new Sonata\EasyExtendsBundle\SonataEasyExtendsBundle(),
            // ...
        );
    }


Generate the ApplicationSonataUserBundle
----------------------------------------

At this point, the bundle is not yet ready. You need to generate the correct
entities for the media::

    php app/console sonata:easy-extends:generate SonataUserBundle

If you specify no parameter, the files are generated in app/Application/Sonata...
but you can specify the path with ``--dest=src``

.. note::

    The command will generate domain objects in an ``Application`` namespace.
    So you can point entities' associations to a global and common namespace.
    This will make Entities sharing easier as your models will allow to
    point to a global namespace. For instance the user will be
    ``Application\Sonata\UserBundle\Entity\User``.


Set up the ApplicationSonataUserBundle
--------------------------------------

Now, add the new `Application` Bundle into the kernel

.. code-block:: php

  <?php
  public function registerbundles()
  {
      return array(
          // Application Bundles
          new Application\Sonata\UserBundle\ApplicationSonataUserBundle(),

          // Vendor specifics bundles
          new Sonata\UserBundle\SonataUserBundle(),
          new Sonata\AdminBundle\SonataAdminBundle(),
          new Sonata\EasyExtendsBundle\SonataEasyExtendsBundle(),
      );
  }

Update the ``autoload.php`` to add a new namespace:

.. code-block:: php

  <?php
  $loader->registerNamespaces(array(
    'Sonata'        => __DIR__.'../vendor/bundles',
    'Application'   => __DIR__,

    // ... other declarations
  ));


Configuration
-------------

Then add these bundles in the config mapping definition (or enable `auto_mapping <http://symfony.com/doc/2.0/reference/configuration/doctrine.html#configuration-overview>`_):

.. code-block:: yaml

    # app/config/config.yml

    doctrine:
        orm:
            entity_managers:
                default:
                    mappings:
                        ApplicationSonataUserBundle: ~
                        SonataUserBundle: ~
