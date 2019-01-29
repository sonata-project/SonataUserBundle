UPGRADE 4.x
===========

### Deprecated `TwoStepVerificationCommand` with no arguments

Attempting to run that command with 2FA enabled and without building the
command with its `$helper` and `$userManager` arguments beforehand is
deprecated. Make sure you provide both arguments.

### Deprecated `UserGenderListType`

Relying on this class to provide user gender list on sonata forms is
deprecated and will be removed in 5.0. You should use
`Symfony\Component\Form\Extension\Core\Type\ChoiceType` instead.
