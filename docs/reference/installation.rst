.. index::
    single: Installation
    single: Configuration

Installation
============

Prerequisites
-------------

PHP ^7.2 and Symfony ^4.4 are needed to make this bundle work, there are
also some Sonata dependencies that need to be installed and configured beforehand.

Required dependencies:

* `SonataAdminBundle <https://sonata-project.org/bundles/admin>`_

Optional dependencies:

* `SonataGoogleAuthenticator <https://github.com/sonata-project/GoogleAuthenticator>`_

And the persistence bundle (choose one):

* `SonataDoctrineOrmAdminBundle <https://sonata-project.org/bundles/doctrine-orm-admin>`_
* `SonataDoctrinePHPCRAdminBundle <https://sonata-project.org/bundles/doctrine-phpcr-admin>`_
* `SonataDoctrineMongoDBAdminBundle <https://sonata-project.org/bundles/mongo-admin>`_

Follow also their configuration step; you will find everything you need in
their own installation chapter.

.. note::

    If a dependency is already installed somewhere in your project or in
    another dependency, you won't need to install it again.

Enable the Bundle
-----------------

Add ``SonataUserBundle`` via composer::

    composer require sonata-project/user-bundle

.. note::

    This will install the FOSUserBundle_, too.

If you want to use the REST API, you also need ``friendsofsymfony/rest-bundle`` and ``nelmio/api-doc-bundle``::

    composer require friendsofsymfony/rest-bundle nelmio/api-doc-bundle

Next, be sure to enable the bundles in your ``config/bundles.php`` file if they
are not already enabled::

    // config/bundles.php

    return [
        // ...
        FOS\UserBundle\FOSUserBundle::class => ['all' => true],
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
            group: App\Entity\SonataUserGroup

FOSUserBundle Configuration
---------------------------

.. code-block:: yaml

    # config/packages/fos_user.yaml

    fos_user:
        db_driver: orm
        firewall_name: main
        user_class: App\Entity\SonataUserUser
        group:
            group_class: App\Entity\SonataUserGroup
            group_manager: sonata.user.orm.group_manager
        service:
            user_manager: sonata.user.orm.user_manager
        from_email:
            address: "%mailer_user%"
            sender_name: "%mailer_user%"

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
                        FOSUserBundle: ~

And then create the corresponding entities, ``src/Entity/SonataUserUser``::

    // src/Entity/SonataUserUser.php

    use Doctrine\ORM\Mapping as ORM;
    use Sonata\UserBundle\Entity\BaseUser;

    /**
     * @ORM\Entity
     * @ORM\Table(name="fos_user__user")
     */
    class SonataUserUser extends BaseUser
    {
        /**
         * @ORM\Id
         * @ORM\GeneratedValue
         * @ORM\Column(type="integer")
         */
        protected $id;
    }

and ``src/Entity/SonataUserGroup``::

    // src/Entity/SonataUserGroup.php

    use Doctrine\ORM\Mapping as ORM;
    use Sonata\UserBundle\Entity\BaseGroup;

    /**
     * @ORM\Entity
     * @ORM\Table(name="fos_user__group")
     */
    class SonataUserGroup extends BaseGroup
    {
        /**
         * @ORM\Id
         * @ORM\GeneratedValue
         * @ORM\Column(type="integer")
         */
        protected $id;
    }

The only thing left is to update your schema::

    bin/console doctrine:schema:update --force

Doctrine MongoDB Configuration
------------------------------

You have to create the corresponding documents, ``src/Document/SonataUserUser``::

    // src/Document/SonataUserUser.php

    use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
    use Sonata\UserBundle\Document\BaseUser;

    /**
     * @MongoDB\Document
     */
    class SonataUserUser extends BaseUser
    {
        /**
         * @MongoDB\Id
         */
        protected $id;
    }

and ``src/Document/SonataUserGroup``::

    // src/Document/SonataUserGroup.php

    use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
    use Sonata\UserBundle\Document\BaseGroup;

    /**
     * @MongoDB\Document
     */
    class SonataUserGroup extends BaseGroup
    {
        /**
         * @MongoDB\Id
         */
        protected $id;
    }

Then configure ``SonataUserBundle`` to use the newly generated classes::

    # config/packages/sonata_user.yaml

    sonata_user:
        manager_type: mongodb
        class:
            user: App\Document\SonataUserUser
            group: App\Document\SonataUserGroup

And configure ``FOSUserBundle``::

    # config/packages/fos_user.yaml

    fos_user:
        db_driver: mongodb
        user_class: App\Document\SonataUserUser
        group:
            group_class: App\Document\SonataUserGroup

ACL Configuration
-----------------

When using ACL, the ``UserBundle`` can prevent `normal` users to change
settings of `super-admin` users, to enable this use the following configuration:

.. code-block:: yaml

    # config/packages/sonata_user.yaml

    sonata_user:
        security_acl: true
        manager_type: orm # can be orm or mongodb

.. code-block:: yaml

    # config/packages/security.yaml

    security:
        encoders:
            FOS\UserBundle\Model\UserInterface: sha512

        acl:
            connection: default

Mailer Configuration
--------------------

You can define a custom mailer to send reset password emails.
Your mailer will have to implement ``FOS\UserBundle\Mailer\MailerInterface``.

.. code-block:: yaml

    # config/packages/sonata_user.yaml

    sonata_user:
        mailer: custom.mailer.service.id

Integrating the bundle into the Sonata Admin Bundle
---------------------------------------------------

Add the related security routing information:

.. code-block:: yaml

    # config/routes.yaml

    sonata_user_admin_security:
        resource: '@SonataUserBundle/Resources/config/routing/admin_security.xml'
        prefix: /admin

    sonata_user_admin_resetting:
        resource: '@SonataUserBundle/Resources/config/routing/admin_resetting.xml'
        prefix: /admin/resetting

Then, add a new custom firewall handlers for the admin:

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
.. _FOSUserBundle: https://github.com/FriendsOfSymfony/FOSUserBundle
.. _`auto_mapping`: http://symfony.com/doc/4.4/reference/configuration/doctrine.html#configuration-overviews
