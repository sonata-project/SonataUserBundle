.. index::
    single: Installation
    single: Configuration

Installation
============

Prerequisites
-------------

If you're planning on using this bundle with SonataAdminBundle, you may want to install
and configure that bundle first.

* `SonataAdminBundle <https://docs.sonata-project.org/projects/SonataAdminBundle/en/4.x/>`_

and the persistence bundle (choose one):

* `SonataDoctrineORMAdminBundle <https://docs.sonata-project.org/projects/SonataDoctrineORMAdminBundle/en/4.x/>`_
* `SonataDoctrineMongoDBAdminBundle <https://docs.sonata-project.org/projects/SonataDoctrineMongoDBAdminBundle/en/4.x/>`_

Follow also their configuration step; you will find everything you need in
their own installation chapter.

.. note::

    If a dependency is already installed somewhere in your project or in
    another dependency, you won't need to install it again.

Enable the Bundle
-----------------

Add ``SonataUserBundle`` via composer::

    composer require sonata-project/user-bundle

Next, be sure to enable the bundles in your ``config/bundles.php`` file if they
are not already enabled::

    // config/bundles.php

    return [
        // ...
        Sonata\UserBundle\SonataUserBundle::class => ['all' => true],
    ];

Configuration
=============

SonataUserBundle Configuration
------------------------------

.. code-block:: yaml

    # config/packages/sonata_user.yaml

    sonata_user:
        class:
            user: App\Entity\SonataUserUser
        resetting:
            email:
                address: sonata@localhost
                sender_name: Sonata Admin

Doctrine ORM Configuration
--------------------------

And these in the config mapping definition (or enable `auto_mapping`_)::

    # config/packages/doctrine.yaml

    doctrine:
        orm:
            entity_managers:
                default:
                    mappings:
                        SonataUserBundle: ~

And then create the corresponding entity, ``src/Entity/SonataUserUser``::

    // src/Entity/SonataUserUser.php

    use Doctrine\DBAL\Types\Types;
    use Doctrine\ORM\Mapping as ORM;
    use Sonata\UserBundle\Entity\BaseUser;
    // or `Sonata\UserBundle\Entity\BaseUser3` as BaseUser if you upgrade to doctrine/orm ^3

    #[ORM\Entity]
    #[ORM\Table(name: 'user__user')]
    class SonataUserUser extends BaseUser
    {
        #[ORM\Id]
        #[ORM\Column(type: Types::INTEGER)]
        #[ORM\GeneratedValue]
        protected $id;
    }

The only thing left is to update your schema::

    bin/console doctrine:schema:update --force

Doctrine MongoDB Configuration
------------------------------

You have to create the corresponding document, ``src/Document/SonataUserUser``::

    // src/Document/SonataUserUser.php

    use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
    use Sonata\UserBundle\Document\BaseUser;

    #[ODM\Document]
    class SonataUserUser extends BaseUser
    {
        #[ODM\Id]
        protected $id;
    }

Then configure ``SonataUserBundle`` to use the newly generated classes::

    # config/packages/sonata_user.yaml

    sonata_user:
        manager_type: mongodb
        class:
            user: App\Document\SonataUserUser

Integrating the bundle into the Sonata Admin Bundle
---------------------------------------------------

.. note::

    If you're using this bundle without the optional Sonata Admin Bundle,
    please, ignore this section.

Add the related security routing information:

.. code-block:: yaml

    # config/routes.yaml

    sonata_user_admin_security:
        resource: '@SonataUserBundle/Resources/config/routing/admin_security.xml'
        prefix: /admin

    sonata_user_admin_resetting:
        resource: '@SonataUserBundle/Resources/config/routing/admin_resetting.xml'
        prefix: /admin

Then, add a new custom firewall handlers for the admin:

.. code-block:: yaml

    # config/packages/security.yaml

    security:
        enable_authenticator_manager: true
        firewalls:
            admin:
                lazy: true
                pattern: /admin(.*)
                provider: sonata_user_bundle
                context: user
                form_login:
                    login_path: sonata_user_admin_security_login
                    check_path: sonata_user_admin_security_check
                    default_target_path: sonata_admin_dashboard
                logout:
                    path: sonata_user_admin_security_logout
                    target: sonata_user_admin_security_login
                remember_me:
                    secret: '%env(APP_SECRET)%'
                    lifetime: 2629746
                    path: /admin

.. note::

    If you run under the old authentication system (Symfony 4.4 or
    Symfony 5.4 with `enable_authenticator_manager` set to `false`)
    you should add `anonymous` set to `true` inside the admin firewall.

Add role hierarchy, hasher and provider:

.. code-block:: yaml

    # config/packages/security.yaml

    security:
        role_hierarchy:
            ROLE_ADMIN: [ROLE_USER, ROLE_SONATA_ADMIN]
            ROLE_SUPER_ADMIN: [ROLE_ADMIN, ROLE_ALLOWED_TO_SWITCH]

        password_hashers:
            Sonata\UserBundle\Model\UserInterface: auto

        providers:
            sonata_user_bundle:
                id: sonata.user.security.user_provider

.. note::

    If you run under Symfony 4.4, `password_hashers` keyword inside `security`
    does not exist, instead replace on the above configuration with `encoders`.

The last part is to define 4 new access control rules:

.. code-block:: yaml

    # config/packages/security.yaml

    security:
        access_control:
            # Admin login page needs to be accessed without credential
            - { path: ^/admin/login$, role: PUBLIC_ACCESS }
            - { path: ^/admin/logout$, role: PUBLIC_ACCESS }
            - { path: ^/admin/login_check$, role: PUBLIC_ACCESS }
            - { path: ^/admin/request$, role: PUBLIC_ACCESS }
            - { path: ^/admin/check-email$, role: PUBLIC_ACCESS }
            - { path: ^/admin/reset/.*$, role: PUBLIC_ACCESS }

            # Secured part of the site
            # This config requires being logged for the whole site and having the admin role for the admin part.
            # Change these rules to adapt them to your needs
            - { path: ^/admin/, role: ROLE_ADMIN }
            - { path: ^/.*, role: PUBLIC_ACCESS }

.. note::

    If you run under Symfony 4.4, `PUBLIC_ACCESS` role does not exist, instead
    replace on the above configuration with `IS_AUTHENTICATED_ANONYMOUSLY`.

Mailer Configuration
--------------------

You can define a custom mailer to send reset password emails.
Your mailer will have to implement ``Sonata\UserBundle\Mailer\MailerInterface``.

.. code-block:: yaml

    # config/packages/sonata_user.yaml

    sonata_user:
        mailer: custom.mailer.service.id

ACL Configuration
-----------------

When using ACL, the ``UserBundle`` can prevent `normal` users to change
settings of `super-admin` users, to enable this use the following configuration:

.. code-block:: yaml

    # config/packages/sonata_user.yaml

    sonata_user:
        security_acl: true

.. code-block:: yaml

    # config/packages/security.yaml

    security:
        acl:
            connection: default

Using the roles
---------------

Each admin has its own roles, use the user form to assign them to other
users. The available roles to assign to others are limited to the roles
available to the user editing the form.

Next Steps
----------

At this point, your Symfony installation should be fully functional, without errors
showing up from SonataUserBundle. If, at this point or during the installation,
you come across any errors, don't panic:

    - Read the error message carefully. Try to find out exactly which bundle is causing the error.
      Is it SonataUserBundle or one of the dependencies?
    - Make sure you followed all the instructions correctly, for both SonataUserBundle and its dependencies.
    - Still no luck? Try checking the project's `open issues on GitHub`_.

.. _`open issues on GitHub`: https://github.com/sonata-project/SonataUserBundle/issues
.. _`auto_mapping`: http://symfony.com/doc/4.4/reference/configuration/doctrine.html#configuration-overviews
