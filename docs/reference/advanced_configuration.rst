.. index::
    single: Advanced configuration
    single: Options

Advanced Configuration
======================

Full configuration options:

.. code-block:: yaml

    fos_user:
        db_driver:        orm # can be orm or mongodb (support is also available within FOSUser for couchdb, propel but none is given for SonataUserBundle)
        firewall_name:    main
        user_class:       Application\Sonata\UserBundle\Entity\User

        group:
            group_class:  Application\Sonata\UserBundle\Entity\Group

        profile:
            # Authentication Form
            form:
                type:               fos_user_profile
                name:               fos_user_profile_form
                validation_groups:  [Authentication] # Please note : this is not the default value

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


    jms_serializer:
        metadata:
            directories:
                - { name: 'sonata_datagrid', path: "%kernel.project_dir%/vendor/sonata-project/datagrid-bundle/src/Resources/config/serializer", namespace_prefix: 'Sonata\DatagridBundle' }
                - { name: 'sonata_user', path: "%kernel.project_dir%/vendor/sonata-project/user-bundle/src/Resources/config/serializer", namespace_prefix: 'Sonata\UserBundle' }
                # override FOSUser default serialization
                - { name: 'fos_user', path: "%kernel.project_dir%/vendor/sonata-project/user-bundle/src/Resources/config/serializer/FOSUserBundle", namespace_prefix: 'FOS\UserBundle' }

    # Enable Doctrine to map the provided entities
    doctrine:
        orm:
            entity_managers:
                default:
                    mappings:
                        FOSUserBundle: ~
                        ApplicationSonataUserBundle: ~
                        SonataUserBundle: ~
