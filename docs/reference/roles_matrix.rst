Roles Matrix
============

The ``Sonata\UserBundle\Form\Type\RolesMatrixType`` was built to show all roles in a matrix view.

![Roles matrix](../images/roles_matrix.png)

Every admin has defined default roles like:

    - EDIT
    - LIST
    - CREATE
    - VIEW
    - DELETE
    - EXPORT
    - ALL

The roles matrix consists of two parts:

1. shows the matrix with each admin and their permissions.
2. shows the custom roles which are configured in ``security.yml`` and list them as checkboxes (+ showing their inherited roles).

.. note::

    If the current logged in user is not granted to an admin the checkbox is disabled.

How to exclude an admin
-----------------------

To exclude an admin you can add ``show_in_roles_matrix`` option like this:

.. configuration-block::

    .. code-block:: yaml

        # config/services.yaml

        services:
            app.admin.post:
                class: App\Admin\PostAdmin
                tags:
                    - { name: sonata.admin, manager_type: orm, label: "Post", show_in_roles_matrix: false }
                arguments:
                    - ~
                    - App\Entity\Post
                    - ~
                public: true
