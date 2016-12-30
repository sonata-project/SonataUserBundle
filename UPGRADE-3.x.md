UPGRADE 3.x
===========

UPGRADE FROM 3.1 to 3.2
=======================

### Deprecated sonata_basket_delivery_redirect session variable

Relying on this variable to get a redirection after registration is deprecated
and will be removed in 4.0.

UPGRADE FROM 3.0 to 3.1
=======================

### Tests

All files under the ``Tests`` directory are now correctly handled as internal test classes.
You can't extend them anymore, because they are only loaded when running internal tests.
More information can be found in the [composer docs](https://getcomposer.org/doc/04-schema.md#autoload-dev).
