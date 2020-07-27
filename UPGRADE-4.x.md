UPGRADE 4.x
===========

UPGRADE FROM 4.6 to 4.7
========================

### SonataEasyExtends is deprecated

Registering `SonataEasyExtendsBundle` bundle is deprecated, it SHOULD NOT be registered.
Register `SonataDoctrineBundle` bundle instead.

UPGRADE FROM 4.2.3 to 4.3
=========================

### Deprecated `TwoStepVerificationCommand` with no arguments

Attempting to run that command with 2FA enabled and without building the
command with its `$helper` and `$userManager` arguments beforehand is
deprecated. Make sure you provide both arguments.

UPGRADE FROM 4.1 to 4.1.1
=========================

### Deprecated `UserGenderListType`

Relying on this class to provide user gender list on sonata forms is
deprecated and will be removed in 5.0. You should use
`Symfony\Component\Form\Extension\Core\Type\ChoiceType` instead.
