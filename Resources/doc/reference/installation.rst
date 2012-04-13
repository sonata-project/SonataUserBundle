Installation
============

Set up the SonataUserBundle
---------------------------

To begin, add the dependent bundles to the ``vendor/bundles`` directory. Add
the following lines to the file ``deps``::

    [SonataUserBundle]
        git=git://github.com/sonata-project/SonataUserBundle.git
        target=/bundles/Sonata/UserBundle
        version=origin/2.0

    [SonataEasyExtendsBundle]
        git=git://github.com/sonata-project/SonataEasyExtendsBundle.git
        target=/bundles/Sonata/EasyExtendsBundle

    [SonataAdminBundle]
        git=git://github.com/sonata-project/SonataAdminBundle.git
        target=/bundles/Sonata/AdminBundle
        version=origin/2.0

and run::

  bin/vendors install

Autoload and AppKernel Configuration
------------------------------------

Next, be sure to enable the bundles in your autoload.php and AppKernel.php
files:

.. code-block:: php

    <?php
    // app/autoload.php
    $loader->registerNamespaces(array(
        // ...
        'Sonata'        => __DIR__.'/../vendor/bundles',
        'Application'   => __DIR__,
        // ...
    ));


    // app/appkernel.php
    public function registerbundles()
    {
        return array(
            // ...
            // You have 2 options to initialize the SonataUserBundle in your AppKernel,
            // you can select which bundle SonataUserBundle extends
            // extend the ``FOSUserBundle``
            new Sonata\UserBundle\SonataUserBundle('FOSUserBundle'),
            // OR
            // the bundle will NOT extend ``FOSUserBundle``
            new Sonata\UserBundle\SonataUserBundle(),

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
          // ...
          new Application\Sonata\UserBundle\ApplicationSonataUserBundle(),
          // ...

      )
  }


Acl Configuration
-----------------

When using ACL, the UserBundle can prevent ``normal`` user to change settings of ``super-admin`` users, to enable this
add to the configuration:

.. code-block:: yaml

    # app/config/config.yml
    sonata_user:
        security_acl: true


    # app/config/security.yml
    security:
        # [...]
        acl:
            connection: default

Doctrine Configuration
----------------------

Then add these bundles in the config mapping definition (or enable `auto_mapping <http://symfony.com/doc/2.0/reference/configuration/doctrine.html#configuration-overview>`_):

.. code-block:: yaml

    # app/config/config.yml

    fos_user:
        db_driver:      orm # can be orm or odm
        firewall_name:  main
        user_class:     Application\Sonata\UserBundle\Entity\User

        group:
            group_class: Application\Sonata\UserBundle\Entity\Group

    doctrine:
        orm:
            entity_managers:
                default:
                    mappings:
                        ApplicationSonataUserBundle: ~
                        SonataUserBundle: ~


Integrating the bundle into the Sonata Admin Bundle
---------------------------------------------------

Add the related security routing information

.. code-block:: yaml

    sonata_user:
        resource: '@SonataUserBundle/Resources/config/routing/admin_security.xml'
        prefix: /admin

You also need to define a ``sonata_user_impersonating`` route, used as a redirection after an user impersonating.

Then add a new custom firewall handlers for the admin

.. code-block:: yaml

    security:
        role_hierarchy:
            ROLE_ADMIN:       [ROLE_USER, ROLE_SONATA_ADMIN]
            ROLE_SUPER_ADMIN: [ROLE_ADMIN, ROLE_ALLOWED_TO_SWITCH]
            SONATA:
                - ROLE_SONATA_PAGE_ADMIN_PAGE_EDIT  # if you are using acl then this line must be commented

        providers:
            fos_userbundle:
                id: fos_user.user_manager

        firewalls:
            # -> custom firewall for the admin area of the URL
            admin:
                switch_user:        true
                context:            user
                pattern:            /admin(.*)
                form_login:
                    provider:       fos_userbundle
                    login_path:     /admin/login
                    use_forward:    false
                    check_path:     /admin/login_check
                    failure_path:   null
                    use_referer:    true
                logout:
                    path:           /admin/logout
                    target:         /admin/login

                anonymous:    true
            # -> end custom configuration

            # defaut login area for standard users
            main:
                switch_user:        true
                context:            user
                pattern:            .*
                form_login:
                    provider:       fos_userbundle
                    login_path:     /login
                    use_forward:    false
                    check_path:     /login_check
                    failure_path:   null
                logout:             true
                anonymous:          true

The last part is to define 3 new access control rules :

.. code-block:: yaml

    security:
        access_control:
            # URL of FOSUserBundle which need to be available to anonymous users
            - { path: ^/_wdt, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/_profiler, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/login$, role: IS_AUTHENTICATED_ANONYMOUSLY }

            # -> custom access control for the admin area of the URL
            - { path: ^/admin/login$, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/admin/logout$, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/admin/login-check$, role: IS_AUTHENTICATED_ANONYMOUSLY }
            # -> end

            - { path: ^/register, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/resetting, role: IS_AUTHENTICATED_ANONYMOUSLY }

            # Secured part of the site
            # This config requires being logged for the whole site and having the admin role for the admin part.
            # Change these rules to adapt them to your needs
            - { path: ^/admin, role: [ROLE_ADMIN, ROLE_SONATA_ADMIN] }
            - { path: ^/.*, role: IS_AUTHENTICATED_ANONYMOUSLY }


Using the roles
---------------

Each admin has its own roles, use the user form to assign them to other users. The available roles to assign to others
are limited to the roles available to the user editing the form.
