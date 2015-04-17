UPGRADE FROM 3.x to 4.0
=======================

## Deprecations

All the deprecated code introduced on 3.x is removed on 4.0.

Please read [3.x](https://github.com/sonata-project/SonataUserBundle/tree/3.x) upgrade guides for more information.

See also the [diff code](https://github.com/sonata-project/SonataUserBundle/compare/3.x...4.0.0).

## Google Authenticator

The google auth dependency is optional now, please add it to you composer file if you need this.

## FOSUser 2.0

FOSUser is now on its 2.0 version. You can check its UPGRADE notes [here](https://github.com/FriendsOfSymfony/FOSUserBundle/blob/v2.0.0/Upgrade.md).

## Bundle definition

SonataUser no longer has FOSUser as its parent bundle. It requires FOSUser as a dependency always.

## Removals

Removed code used to define custom User login for the frontend application. Use FOSUser directly instead.

## UserInterface

If you have implemented a custom user, you must adapt the signature of new methods to match the one in `UserInterface` again
