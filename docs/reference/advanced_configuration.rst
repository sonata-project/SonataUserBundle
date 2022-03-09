.. index::
    single: Advanced configuration
    single: Options

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
