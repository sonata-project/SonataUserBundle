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
            template:       '@SonataUser/Profile/action.html.twig' # or '@SonataUser/Profile/action_with_customer_menu.html.twig'
            menu_builder:   'sonata.user.profile.menu_builder.default'

            menu:
                -
                    route: 'sonata_user_profile_dashboard'
                    label: 'link_show_profile'
                    domain: 'SonataUserBundle'
                    route_parameters:  {}

            blocks:
                -
                    position: left
                    type: sonata.user.block.account
                    settings:
                        template: '@SonataUser/Block/account_dashboard.html.twig'
                -
                    position: right
                    type: sonata.block.service.text
                    settings:
                        content: '<h2>Welcome!</h2> This is a sample user profile dashboard, feel free to override it in the configuration!'

        mailer: sonata.user.mailer.default # Service used to send emails

    # override FOSUser default serialization
    jms_serializer:
        metadata:
            directories:
                App:
                    path: "%kernel.root_dir%/../vendor/sonata-project/user-bundle/Sonata/UserBundle/Resources/config/serializer/FOSUserBundle"
                    namespace_prefix: 'FOS\UserBundle'

    # Enable Doctrine to map the provided entities
    doctrine:
        orm:
            entity_managers:
                default:
                    mappings:
                        FOSUserBundle: ~
                        ApplicationSonataUserBundle: ~
                        SonataUserBundle: ~
