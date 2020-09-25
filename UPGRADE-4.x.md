UPGRADE 4.x
===========

UPGRADE FROM 4.x to 4.x
=======================

### Dependencies

- "sonata-project/datagrid-bundle" is bumped from ^2.4 to ^3.0.

  If you are extending these methods, you MUST add argument and return type declarations:
  - `Sonata\UserBundle\Entity\UserManager::getPager()`
  - `Sonata\UserBundle\Entity\GroupManager::getPager()`

- Added support for "nelmio/api-doc-bundle" ^3.6.

  Controllers for NelmioApiDocBundle v2 were moved under `Sonata\UserBundle\Controller\Api\Legacy\` namespace and controllers for NelmioApiDocBundle v3 were added as replacement. If you extend them, you must ensure they are using the corresponding inheritance.

### Fix REST API routing paths

In version 4.6.0 some extra REST API routes were added by mistake, creating new duplicated paths pointing to existing actions (by instance `/groups/{id}.{_format}` was duplicated as `/group/{id}.{_format}`, `/users/{id}.{_format}` was duplicated as `/user/{id}.{_format}`, etc).
You MUST avoid importing these routes in order to keep your API routing clean and consistent with previous versions. To do so, make sure to import some of these routing files, depending on your needs:

    sonata_api_user:
        prefix: /api/user
        resource: "@SonataUserBundle/Resources/config/routing/standard_api.xml"
        # or for NelmioApiDocBundle v3
        #resource: "@SonataUserBundle/Resources/config/routing/standard_api_nelmio_v3.xml"

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
