Advanced Configuration
======================

Full configuration options:

.. code-block:: yaml

    fos_user:
        db_driver:        orm # can be orm or odm
        firewall_name:    main
        user_class:       Application\Sonata\UserBundle\Entity\User

        group:
            group_class:  Application\Sonata\UserBundle\Entity\Group

    sonata_user:
        security_acl:     false
        class:
            user:         Application\Sonata\UserBundle\Entity\User
            group:        Application\Sonata\UserBundle\Entity\Group

    # Enable Doctrine to map the provided entities
    doctrine:
        orm:
            entity_managers:
                default:
                    mappings:
                        FOSUserBundle: ~
                        ApplicationSonataUserBundle: ~
                        SonataUserBundle: ~
