.. index::
    single: Advanced configuration
    single: Options

Advanced Configuration
======================

Full configuration options:

.. code-block:: yaml

    sonata_user:
        security_acl: false
        manager_type: orm      # can be orm or mongodb

        table:
            user_group: "my_custom_user_group_association_table_name"

        impersonating:
            route:                page_slug
            parameters:           { path: / }

        class:                  # Entity Classes
            user:               Application\Sonata\UserBundle\Entity\User
            group:              Application\Sonata\UserBundle\Entity\Group

        admin:                  # Admin Classes
            user:
                class:          Sonata\UserBundle\Admin\Entity\UserAdmin
                controller:     Sonata\AdminBundle\Controller\CRUDController
                translation:    SonataUserBundle

            group:
                class:          Sonata\UserBundle\Admin\Entity\GroupAdmin
                controller:     Sonata\AdminBundle\Controller\CRUDController
                translation:    SonataUserBundle

        profile:
            default_avatar: 'bundles/sonatauser/default_avatar.png' # Default avatar displayed if the user doesn't have one

        mailer: sonata.user.mailer.default # Service used to send emails

    # Enable Doctrine to map the provided entities
    doctrine:
        orm:
            entity_managers:
                default:
                    mappings:
                        ApplicationSonataUserBundle: ~
                        SonataUserBundle: ~
