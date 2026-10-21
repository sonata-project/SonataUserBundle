UPGRADE 5.x
===========

## `BaseUser` ORM mapping deprecated

The ORM mapping of `Sonata\UserBundle\Entity\BaseUser` is deprecated and will be removed in 6.0.
Please extend `Sonata\UserBundle\Entity\BaseUser3` instead.

Before:

```php
use Sonata\UserBundle\Entity\BaseUser;

class User extends BaseUser
{
}
```

After:

```php
use Sonata\UserBundle\Entity\BaseUser3;

class User extends BaseUser3
{
}
```

Make sure you run your migrations after the change.
