Advanced Configuration
======================

Full configuration options:

.. code-block:: yaml

    parameters:
        sonata.user.form.type.security_roles.class: Sonata\UserBundle\Form\Type\SecurityRolesType

        sonata.user.profile.form.type.class:            Sonata\UserBundle\Form\Type\ProfileType
        sonata.user.profile.form.handler.default.class: Sonata\UserBundle\Form\Handler\ProfileFormHandler

        sonata.user.admin.user.class:              Sonata\UserBundle\Admin\Entity\UserAdmin
        sonata.user.admin.user.controller:         SonataAdminBundle:CRUD
        sonata.user.admin.user.translation_domain: SonataUserBundle

        sonata.user.admin.group.class:              Sonata\UserBundle\Admin\Entity\GroupAdmin
        sonata.user.admin.group.controller:         SonataAdminBundle:CRUD
        sonata.user.admin.group.translation_domain: %sonata.user.admin.user.translation_domain%

        sonata.user.admin.groupname: sonata_user

    fos_user:
        db_driver:        orm # can be orm or odm
        firewall_name:    main
        user_class:       Application\Sonata\UserBundle\Entity\User

        group:
            group_class:  Application\Sonata\UserBundle\Entity\Group

        profile:  # Authentication Form
            form:
                type:               fos_user_profile
                handler:            fos_user.profile.form.handler.default
                name:               fos_user_profile_form
                validation_groups:  [Authentication] # Please note : this is not the default value

    sonata_user:
        security_acl:           false
        impersonating_route:    homepage # or any route you want to use
        class:
            user:               Application\Sonata\UserBundle\Entity\User
            group:              Application\Sonata\UserBundle\Entity\Group

        profile:  # Profile Form (firstname, lastname, etc ...)
            form:
                type:               sonata.user.profile
                handler:            sonata.user.profile.form.handler.default
                name:               sonata_user_profile_form
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
