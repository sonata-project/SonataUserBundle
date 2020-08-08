.. index::
    single: Two-step validation
    single: Authentication
    single: Google

Two-step Validation (with Google Authenticator)
===============================================

The ``SonataUserBundle`` provides an optional layer of security by including a support for a Two-step Validation process.

When the option is enabled, the login process is done with the following workflow:

* the user enters the login and password
* if the user get the correct credentials, then a code validation form is displayed
* at this point, the user must enter a time based code provided by the Google Authenticator application
* the code is valid only once per minute

So if your login and password are compromised then the hacker must also hold your phone!


Installation
------------

.. code-block:: bash

    composer require sonata-project/google-authenticator

Edit the configuration file:

.. code-block:: yaml

    # config/packages/sonata_user.yaml

    sonata_user:
        google_authenticator:
            enabled: true
            server:  yourserver.com
            trusted_ip_list:
                - 127.0.0.1
            forced_for_role:
                - ROLE_ADMIN

Also, if you want to use ``trusted_ip_list`` and ``forced_for_role``
configuration nodes for automatically setting the secret to user
(secret - a connection between user and device that will scans QR-code)
and showing QR-code in login form, you need to set the success handler
in your firewall to ``sonata.user.google.authenticator.success_handler``, example:

.. code-block:: yaml

    # config/packages/security.yaml

    security:
        firewalls:
            # Disabling the security for the web debug toolbar, the profiler and Assetic.
            dev:
                pattern:  ^/(_(profiler|wdt)|css|images|js)/
                security: false

            # -> custom firewall for the admin area of the URL
            admin:
                pattern:             /admin(.*)
                context:             user
                form_login:
                    provider:        fos_userbundle
                    login_path:      /admin/login
                    use_forward:     false
                    check_path:      /admin/login_check
                    failure_path:    null
                    success_handler: sonata.user.google.authenticator.success_handler


Then after success login, if the user needs to use 2FA and has no secret,
a QR code will be shown in the login form.

Now if the ``User::twoStepVerificationCode`` property is not null, then a second form will be displayed.
