Roles Matrix
=====================

Configuration
-------------

The type of form ``Sonata\UserBundle\Form\Type\RolesMatrixType``was built to show all roles in matrix.
Every admin has a few defined default roles like:

    - EDIT
    - CREATE
    - LIST
    - DELETE
    - SHOW
    - EXPORT
    - OPERATOR
    - MASTER

The roles matrix has two parts:

1. shows the matrix with each admin and their roles
2. takes the custom roles which are configured in ``security.yml`` and list them as checkboxes

The role matrix will display all roles for each admin. If the currend logged in user is not granted to an admin the checkbox is not clickable.

Exclude Admin
-------------

To exclude an admin add following option to service declaration of the admin: ``show_in_roles_matrix: false``

![roles matrix](../images/roles_matrix.png)
