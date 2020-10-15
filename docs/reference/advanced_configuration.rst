.. index::
    single: Advanced configuration
    single: Options

Advanced Configuration
======================

Full configuration options:

.. code-block:: yaml

    sonata_user:
        security_acl: false
        firewall_name: 'admin'
        manager_type: 'orm' # can be orm or mongodb

        table:
            user_group: 'my_custom_user_group_association_table_name'

        impersonating:
            route: page_slug
            parameters: { path: / }

        class: # Entity Classes
            user: 'App\Entity\SonataUserUser'
            group: 'App\Entity\SonataUserGroup'

        admin: # Admin Classes
            user:
                class: 'Sonata\UserBundle\Admin\Entity\UserAdmin'
                controller: 'Sonata\AdminBundle\Controller\CRUDController'
                translation: 'SonataUserBundle'

            group:
                class: 'Sonata\UserBundle\Admin\Entity\GroupAdmin'
                controller: 'Sonata\AdminBundle\Controller\CRUDController'
                translation: 'SonataUserBundle'

        profile:
            default_avatar: 'bundles/sonatauser/default_avatar.png' # Default avatar displayed if the user doesn't have one

        mailer: sonata.user.mailer.default # Service used to send emails

        service:
            mailer: 'sonata.user.mailer.default'
            email_canonicalizer': 'sonata.user.util.canonicalizer.default'
            token_generator: 'sonata.user.util.token_generator.default'
            username_canonicalizer: 'sonata.user.util.canonicalizer.default'
            user_manager: 'sonata.user.user_manager.default'

        resetting:
            retry_ttl: 7200
            token_ttl: 86400
            email:
                template: '@SonataUser/Resetting/email.txt.twig'
                from_email:
                    address: 'sonatauser@example.com'
                    sender_name: 'SonataUserBundle'

    # add SonataUser serialization
    jms_serializer:
        metadata:
            directories:
                App:
                    path: "%kernel.root_dir%/../vendor/sonata-project/user-bundle/Sonata/UserBundle/Resources/config/serializer"
                    namespace_prefix: 'Sonata\UserBundle'

    # Enable Doctrine to map the provided entities
    doctrine:
        orm:
            entity_managers:
                default:
                    mappings:
                        SonataUserBundle: ~
