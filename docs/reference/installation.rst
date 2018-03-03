.. index::
    single: Installation

Installation
============

Prerequisites
-------------

PHP 7 and Symfony 2.8, >=3.3 or 4 are needed to make this bundle work, there are
also some Sonata dependencies that need to be installed and configured beforehand:

    - `SonataAdminBundle <https://sonata-project.org/bundles/admin>`_
    - `SonataEasyExtendsBundle <https://sonata-project.org/bundles/easy-extends>`_

You will need to install those in their 2.0 or 3.0 branches. Follow also
their configuration step; you will find everything you need in their own
installation chapter.

.. note::
    If a dependency is already installed somewhere in your project or in
    another dependency, you won't need to install it again.

Enable the Bundle
-----------------

.. code-block:: bash

    composer require sonata-project/user-bundle --no-update
    composer require sonata-project/doctrine-orm-admin-bundle  --no-update # optional
    composer require friendsofsymfony/rest-bundle  --no-update # optional when using api
    composer require nelmio/api-doc-bundle  --no-update # optional when using api
    composer require sonata-project/google-authenticator --no-update  # optional
    composer update

Next, be sure to enable the bundles in your ``bundles.php`` file if they
are not already enabled:

.. code-block:: php

    <?php

    // config/bundles.php

    return [
        //...
        Sonata\AdminBundle\SonataAdminBundle::class => ['all' => true],
        Sonata\CoreBundle\SonataCoreBundle::class => ['all' => true],
        Sonata\BlockBundle\SonataBlockBundle::class => ['all' => true],
        Sonata\EasyExtendsBundle\SonataEasyExtendsBundle::class => ['all' => true],
        FOS\UserBundle\FOSUserBundle::class => ['all' => true],
        Sonata\UserBundle\SonataUserBundle::class => ['all' => true],
    ];

.. note::
    If you are not using Symfony Flex, you should enable bundles in your
    ``AppKernel.php``.

.. code-block:: php

    <?php

    // app/AppKernel.php

    public function registerbundles()
    {
        return [
            new Sonata\AdminBundle\SonataAdminBundle(),
            new Sonata\CoreBundle\SonataCoreBundle(),
            new Sonata\BlockBundle\SonataBlockBundle(),
            new Sonata\EasyExtendsBundle\SonataEasyExtendsBundle(),
            // ...
            new FOS\UserBundle\FOSUserBundle(),
            new Sonata\UserBundle\SonataUserBundle(),
            // ...
        ];
    }

Configuration
-------------

.. note::
    If you are not using Symfony Flex, all configuration in this section should
    be added to ``app/config/config.yml``.

ACL Configuration
~~~~~~~~~~~~~~~~~
When using ACL, the ``UserBundle`` can prevent `normal` users to change
settings of `super-admin` users, to enable this use the following configuration:

.. code-block:: yaml

    # config/packages/sonata.yaml

    sonata_user:
        security_acl: true
        manager_type: orm # can be orm or mongodb

.. code-block:: yaml

    # config/packages/security.yaml

    security:
        # [...]

        encoders:
            FOS\UserBundle\Model\UserInterface: sha512

        acl:
            connection: default

Doctrine Configuration
~~~~~~~~~~~~~~~~~~~~~~

Add these config lines to your Doctrine configuration:

.. code-block:: yaml

    # config/packages/doctrine.yaml

    doctrine:
        #...
        dbal:
            types:
                json: Sonata\Doctrine\Types\JsonType


And these in the config mapping definition (or enable `auto_mapping <http://symfony.com/doc/2.0/reference/configuration/doctrine.html#configuration-overview>`_):

.. code-block:: yaml

    # config/packages/doctrine.yaml

    doctrine:
        #...
        orm:
            entity_managers:
                default:
                    mappings:
                        SonataUserBundle: ~
                        FOSUserBundle: ~

FOSUserBundle Configuration
~~~~~~~~~~~~~~~~~~~~~~~~~~~

Add these config lines to your FOSUserBundle configuration:

.. code-block:: yaml

    # config/packages/fos_user.yaml

    fos_user:
        db_driver:      orm # can be orm or odm
        firewall_name:  main
        user_class:     Sonata\UserBundle\Entity\BaseUser


        group:
            group_class:   Sonata\UserBundle\Entity\BaseGroup
            group_manager: sonata.user.orm.group_manager # If you're using doctrine orm (use sonata.user.mongodb.group_manager for mongodb)

        service:
            user_manager: sonata.user.orm.user_manager

        from_email:
            address: "%mailer_user%"
            sender_name: "%mailer_user%"

Integrating the bundle into the Sonata Admin Bundle
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Add the related security routing information:

.. code-block:: yaml

    # config/routes.yaml

    sonata_user_admin_security:
        resource: '@SonataUserBundle/Resources/config/routing/admin_security.xml'
        prefix: /admin

    sonata_user_admin_resetting:
        resource: '@SonataUserBundle/Resources/config/routing/admin_resetting.xml'
        prefix: /admin/resetting

.. note::
    If you are not using Symfony Flex, routes should be added to ``app/config/routing.yml``.

Then, add a new custom firewall handlers for the admin:

.. note::
    If you are not using Symfony Flex, rest of this configuration should be
    added to ``app/config/security.yml``.

.. code-block:: yaml

    # config/packages/security.yaml

    security:
        firewalls:
            # Disabling the security for the web debug toolbar, the profiler and Assetic.
            dev:
                pattern:  ^/(_(profiler|wdt)|css|images|js)/
                security: false

            # -> custom firewall for the admin area of the URL
            admin:
                pattern:            /admin(.*)
                context:            user
                form_login:
                    provider:       fos_userbundle
                    login_path:     /admin/login
                    use_forward:    false
                    check_path:     /admin/login_check
                    failure_path:   null
                logout:
                    path:           /admin/logout
                    target:         /admin/login
                anonymous:          true

            # -> end custom configuration

            # default login area for standard users

            # This firewall is used to handle the public login area
            # This part is handled by the FOS User Bundle
            main:
                pattern:             .*
                context:             user
                form_login:
                    provider:       fos_userbundle
                    login_path:     /login
                    use_forward:    false
                    check_path:     /login_check
                    failure_path:   null
                logout:             true
                anonymous:          true

Add role hierarchy and provider, if you are not using ACL also add the encoder:

.. code-block:: yaml

    # config/packages/security.yaml

    security:
        role_hierarchy:
            ROLE_ADMIN:       [ROLE_USER, ROLE_SONATA_ADMIN]
            ROLE_SUPER_ADMIN: [ROLE_ADMIN, ROLE_ALLOWED_TO_SWITCH]
            SONATA:
                - ROLE_SONATA_PAGE_ADMIN_PAGE_EDIT  # if you are using acl then this line must be commented

        encoders:
            FOS\UserBundle\Model\UserInterface: bcrypt

        providers:
            fos_userbundle:
                id: fos_user.user_provider.username

The last part is to define 4 new access control rules:

.. code-block:: yaml

    # config/packages/security.yaml

    security:
        access_control:
            # Admin login page needs to be accessed without credential
            - { path: ^/admin/login$, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/admin/logout$, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/admin/login_check$, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/admin/resetting, role: IS_AUTHENTICATED_ANONYMOUSLY }

            # Secured part of the site
            # This config requires being logged for the whole site and having the admin role for the admin part.
            # Change these rules to adapt them to your needs
            - { path: ^/admin/, role: [ROLE_ADMIN, ROLE_SONATA_ADMIN] }
            - { path: ^/.*, role: IS_AUTHENTICATED_ANONYMOUSLY }


Using the roles
---------------

Each admin has its own roles, use the user form to assign them to other
users. The available roles to assign to others are limited to the roles
available to the user editing the form.

Extending the Bundle
--------------------
At this point, the bundle is functional, but not quite ready yet. You need
to generate the correct entities for the media:

.. code-block:: bash

    bin/console sonata:easy-extends:generate SonataUserBundle --dest=src --namespace_prefix=App

.. note::
    If you are not using Symfony Flex, use command without ``--namespace_prefix=App``.

With provided parameters, the files are generated in ``src/Application/Sonata/UserBundle``.

.. note::

    The command will generate domain objects in an ``App\Application`` namespace.
    So you can point entities' associations to a global and common namespace.
    This will make Entities sharing easier as your models will allow
    pointing to a global namespace. For instance, the user will be
    ``App\Application\Sonata\UserBundle\Entity\User``.

.. note::
    If you are not using Symfony Flex, the namespace will be ``Application\Sonata\UserBundle\Entity\User``.

Now, add the new ``Application`` Bundle into the ``bundles.php``:

.. code-block:: php

    <?php

    // config/bundles.php

    return [
        //...
        App\Application\Sonata\UserBundle\ApplicationSonataUserBundle::class => ['all' => true],
    ];

.. note::
    If you are not using Symfony Flex, add the new ``Application`` Bundle into your
    ``AppKernel.php``.

.. code-block:: php

    <?php

    // app/AppKernel.php

    public function registerbundles()
    {
        return [
            // ...
            new Application\Sonata\UserBundle\ApplicationSonataUserBundle(),
            // ...
        ];
    }

If you are not using auto-mapping in doctrine you will have to add it there
too:

.. note::
    If you are not using Symfony Flex, next configuration should be added
    to ``app/config/config.yml``.

.. code-block:: yaml

    # config/packages/doctrine.yaml

    doctrine:
        #...
        orm:
            entity_managers:
                default:
                    mappings:
                        #...
                        ApplicationSonataUserBundle: ~

And configure FOSUserBundle and SonataUserBundle to use the newly generated
User and Group classes:

.. note::
    If you are not using Symfony Flex, add classes without the ``App\``
    part.

.. code-block:: php

    # config/packages/fos_user.yaml

    fos_user:
        #...
        user_class:     App\Application\Sonata\UserBundle\Entity\User

        group:
            group_class:   App\Application\Sonata\UserBundle\Entity\Group
        #...

.. code-block:: php

    # config/packages/sonata.yaml

    sonata_user:
        class:
            user: App\Application\Sonata\UserBundle\Entity\User
            group: App\Application\Sonata\UserBundle\Entity\Group


The only thing left is to update your schema:

.. code-block:: bash

    php bin/console doctrine:schema:update --force
