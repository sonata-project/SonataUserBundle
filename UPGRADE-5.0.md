UPGRADE FROM 4.x to 5.0
=======================

## User class simplified

User class no longer has the Profile or Social fields. If you need them, please extend
the User class and add them. Make sure you run your migrations to remove those fields from
your database.

## FOSUserBundle removal

FOSUserBundle dependency was removed. Main features of that bundle were migrated
inside SonataUserBundle. Make sure you run your migrations and remove FOSUserBundle
from your code and dependencies, unless you need it for other purposes.

## Deprecations

All the deprecated code introduced on 4.x is removed on 5.0.

Please read [4.x](https://github.com/sonata-project/SonataUserBundle/tree/4.x) upgrade guides for more information.

See also the [diff code](https://github.com/sonata-project/SonataUserBundle/compare/4.x...5.0.0).

## BaseUser DB structure

The locale field definition changed. Please make sure you run your migrations.
