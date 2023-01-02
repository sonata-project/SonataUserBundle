UPGRADE FROM 4.x to 5.0
=======================

## `SecurityRolesType` and related classes removed

Now the UserAdmin uses `RolesMatrixType` by default to render the roles on the
User edit form. The old `SecurityRolesType` and its related classes are removed.
If you are customising the UserAdmin, please change your form type for
the roles to `RolesMatrixType`.

## Groups removed

User groups was a feature provided mainly by FOSUserBundle with an integration on the
SonataUserBundle. This feature was already deprecated by the FOSUserBundle in its latest
release.

We removed it on SonataUserBundle 5.0 to avoid the extra complexity. Consider using roles
hierarchy in order to group roles if you really need to.

Make sure to remove your Group class and execute a migration to remove the groups table, also keep in mind to update your configuration to remove any reference to a group.

## User class simplified

User class no longer has the Profile or Social fields. If you need them, please extend
the User class and add them. Make sure you run your migrations to remove those fields from
your database.

## FOSUserBundle removal

FOSUserBundle dependency was removed. Main features of that bundle were migrated
inside SonataUserBundle. Make sure you run your migrations and remove FOSUserBundle
from your code and dependencies, unless you need it for other purposes.

## SonataAdminBundle is optional

SonataAdminBundle (sonata-project/admin-bundle) dependency has become optional. If you're using
that bundle, we recommend requiring it explicitly in your composer.json. Note that attempt to use
`sonata_user.userAdmin` Twig variable will throw a `\LogicException` if SonataAdminBundle is not installed.

## Deprecations

All the deprecated code introduced on 4.x is removed on 5.0.

Please read [4.x](https://github.com/sonata-project/SonataUserBundle/tree/4.x) upgrade guides for more information.

See also the [diff code](https://github.com/sonata-project/SonataUserBundle/compare/4.x...5.0.0).

## BaseUser DB structure

The locale field definition changed. Please make sure you run your migrations.
