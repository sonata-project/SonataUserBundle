The permission matrix
=====================

Configuration
-------------

The type of form ``sonata_security_roles`` was improved to show all roles in matrix.
Indeed, for every admin defined in sonata admin we have eight roles by default:

    - Edit
    - Create
    - List
    - Delete
    - Show
    - Export
    - Operator
    - Master

The permssions are represented in two parts in the following table.
The first part shows all default permissions for each entity.
The second part shows all custom defined roles from the ``security.yml`` configuration.

![The permission matrix](../images/permission_matrix.png)

How to use it
-------------

You need to use the ``sonata_security_roles`` form type.