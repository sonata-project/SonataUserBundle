# Integrate the FOS/UserBundle into the Sonata Project

    - AdminBundle : add user and group management
    - EasyExtends : allows to generate Application level model


# Installation

    you have 2 otpions to initialize the SonataUserBundle, you can select which bundle SonataUserBundle extends

        - new Sonata\UserBundle\SonataUserBundle('FOSUserBundle') : the bundle will extends ``FOSUserBundle``
        - new Sonata\UserBundle\SonataUserBundle() : the bundle will NOT extends ``FOSUserBundle``
