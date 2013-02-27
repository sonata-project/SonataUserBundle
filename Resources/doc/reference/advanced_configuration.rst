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

        profile:  # Authentication Form
            form:
                type:               fos_user_profile
                name:               fos_user_profile_form
                validation_groups:  [Authentication] # Please note : this is not the default value

    sonata_user:
        security_acl:           false

		manager_type: orm # Can be orm for mongodb

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
                controller:     SonataAdminBundle:CRUD
                translation:    SonataUserBundle

            group:
                class:          Sonata\UserBundle\Admin\Entity\GroupAdmin
                controller:     SonataAdminBundle:CRUD
                translation:    SonataUserBundle

        profile:  # Profile Form (firstname, lastname, etc ...)
            form:
                type:               sonata_user_profile
                name:               sonata_user_profile_form
                validation_groups:  [Profile]
            update_password:
                type:               sonata_user_update_password
                name:               sonata_user_update_password_form
                validation_groups:  [Profile]

    # Enable Doctrine to map the provided entities
    doctrine:
        orm:
            entity_managers:
                default:
                    mappings:
                        FOSUserBundle: ~
                        ApplicationSonataUserBundle: ~
                        SonataUserBundle: ~
