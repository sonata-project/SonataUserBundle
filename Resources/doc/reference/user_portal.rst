User Portal
===========

SonataUserBundle provides an easy way to customize the user portal (reachable through the /profile URL).

Configuration
-------------

The default menu offers profile edition & user password forms. Would you like to customize this menu, you can do so in the sonata_user configuration:

.. code-block:: yaml

    sonata_user:
        profile:
        # Customize user portal menu by setting links
        menu:
            - { route: 'sonata_user_profile_edit', label: 'link_edit_profile', domain: 'SonataUserBundle'}
            - { route: 'sonata_user_profile_edit_authentication', label: 'link_edit_authentication', domain: 'SonataUserBundle'}
            - { route: 'user_new_action_route', route_parameters: {'id': 42}, label: "My new user action" }

Would you want to display the menu in your action's template, you should inherit the SonataUserBundle:Profile:action.html.twig template. There you can override the ``sonata_profile_title`` & ``sonata_profile_content`` blocks.

Block
-----

You also have the ability to customize the block responsible for displaying the menu with the following options:

* menu_name
    This takes a Knp menu name as the argument and will completely override the menu.

* menu_class
    The menu <ul> class(es) ; by default set to ``nav nav-list``.

* current_class
    The current <li> element class(es) ; by default set to ``active``.

* first_class and last_class
    The first and last <li> element class(es) in the menu ; by default empty.

Menu Factory
------------

The ProfileMenuBuilder class is responsible for creating the user menu. It offers two public method: ``createProfileMenu`` which generates a new ``ItemInterface`` instance and ``buildProfileMenu`` which configures an existing ``ItemInterface`` instance. The latter method throws an event once configured: ``sonata.user.profile.configure_menu`` of type ``ProfileMenuEvent`` which contains the configured ``ItemInterface`` instance, would you choose to override it.

