.. index::
    single: User
    single: Introduction

Introduction
============

Integrates user and security management in the Sonata Admin Project and adds some features:

 - ``AdminBundle``: add user and group management

The roles to be assigned to users are splitted in 2 parts:

 - **editable:** the roles the current user is allowed to assign to other users
    (permission or role ``MASTER``)
 - **readonly:** the roles assigned to the current user, however the current
    user is only allowed to see them

When using ACL, the UserBundle prevents ``normal`` user to change settings of
``super-admin`` users.
