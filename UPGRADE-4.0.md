UPGRADE FROM 3.x to 4.0
=======================

## Deprecations

All the deprecated code introduced on 3.x is removed on 4.0.

Please read [3.x](https://github.com/sonata-project/SonataUserBundle/tree/3.x) upgrade guides for more information.

See also the [diff code](https://github.com/sonata-project/SonataUserBundle/compare/3.x...4.0.0).

## Google Authenticator

The google auth dependency is optional now, please add it to you composer file if you need this.

## The permission matrix

The type of form ``sonata_security_roles`` was improved to show quite the roles in matrix. 
Please have look at the [docs](Resources/doc/reference/permission_matrix.rst).
