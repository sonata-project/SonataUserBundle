.. index::
    single: Advanced configuration
    single: Options
    single: Override Service

Advanced Configuration
======================

Full configuration options:

.. code-block:: yaml

    # config/packages/sonata_user.yaml

    sonata_user:
        security_acl: false
        manager_type: orm # can be orm or mongodb

        impersonating:
            route: page_slug
            parameters: { path: / }

        class: # Entity Classes
            user: Sonata\UserBundle\Entity\BaseUser

        admin: # Admin Classes
            user:
                class: Sonata\UserBundle\Admin\Entity\UserAdmin
                controller: '%sonata.admin.configuration.default_controller%'
                translation: SonataUserBundle

        profile:
            default_avatar: bundles/sonatauser/default_avatar.png # Default avatar displayed if the user doesn't have one

        mailer: sonata.user.mailer.default # Service used to send emails

        resetting: # Reset password configuration (must be configured)
            email:
                template: '@SonataUser/Admin/Security/Resetting/email.html.twig'
                address: ~
                sender_name: ~

Override Service
======================

If you need to override the service of Sonata User admin, you can do it during the service declaration:

.. code-block:: yaml

    # config/services.yaml

    services:
        sonata.user.admin.user:
            class: Sonata\UserBundle\Admin\Entity\UserAdmin
            tags:
                - name: sonata.admin
                  model_class: '%sonata.user.user.class%'
                  controller: '%sonata.user.admin.user.controller%'
                  manager_type: orm
                  group: sonata_user
                  label: users
                  translation_domain: SonataUserBundle
                  label_translator_strategy: sonata.admin.label.strategy.underscore
                  icon: '<i class=\'fa fa-users\'></i>'
            arguments:
                - '@sonata.user.manager.user'

Please note that parameter ``%sonata.user.user.class%`` and ``%sonata.user.admin.user.controller`` refers to configuration options.
