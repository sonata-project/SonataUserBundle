UPGRADE FROM 5.x to 6.0
=======================

## `BaseUser` removed

The deprecated `Sonata\UserBundle\Entity\BaseUser` ORM mapping (`BaseUser.orm.xml`) is removed.
If your User class extends `Sonata\UserBundle\Entity\BaseUser`, please extend
`Sonata\UserBundle\Entity\BaseUser3` instead and update your configuration to reference your own class.

Make sure you run your migrations after the change.


## `TokenGenerator` is final

The `Sonata\UserBundle\Util\TokenGenerator` class is now `final`.
If you extend it, implement `Sonata\UserBundle\Util\TokenGeneratorInterface` instead
and register your service under the `sonata.user.util.token_generator` id.


## `GlobalVariablesCompilerPass` is final

The `Sonata\UserBundle\DependencyInjection\Compiler\GlobalVariablesCompilerPass` class is now `final`.
If you extend it, register your own compiler pass in your bundle instead
and add the `sonata_user` Twig global there.
