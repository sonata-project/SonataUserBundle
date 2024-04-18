# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [5.12.0](https://github.com/sonata-project/SonataUserBundle/compare/5.11.0...5.12.0) - 2024-04-18
### Added
- [[#1677](https://github.com/sonata-project/SonataUserBundle/pull/1677)] BaseUser3 with roles mapped type as `json` to be compatible with ORM 3 ([@RobinDev](https://github.com/RobinDev))

## [5.11.0](https://github.com/sonata-project/SonataUserBundle/compare/5.10.0...5.11.0) - 2024-03-08
### Added
- [[#1661](https://github.com/sonata-project/SonataUserBundle/pull/1661)] Default value `true` for the "multiple" option at `RolesMatrixType` form type ([@phansys](https://github.com/phansys))

### Changed
- [[#1661](https://github.com/sonata-project/SonataUserBundle/pull/1661)] `RolesMatrixType` form type allows multiple values by default ([@phansys](https://github.com/phansys))

## [5.10.0](https://github.com/sonata-project/SonataUserBundle/compare/5.9.0...5.10.0) - 2023-07-22
### Changed
- [[#1653](https://github.com/sonata-project/SonataUserBundle/pull/1653)] User implements LegacyPasswordAuthenticatedUserInterface ([@Hanmac](https://github.com/Hanmac))

### Fixed
- [[#1651](https://github.com/sonata-project/SonataUserBundle/pull/1651)] Deprecation of Event Subscribers on Symfony 6.3. The UserListener now uses Event Listeners ([@Hanmac](https://github.com/Hanmac))

## [5.9.0](https://github.com/sonata-project/SonataUserBundle/compare/5.8.0...5.9.0) - 2023-06-03
### Added
- [[#1646](https://github.com/sonata-project/SonataUserBundle/pull/1646)] Support for SonataBlockBundle 5.0 ([@jordisala1991](https://github.com/jordisala1991))

## [5.8.0](https://github.com/sonata-project/SonataUserBundle/compare/5.7.0...5.8.0) - 2023-05-13
### Added
- [[#1643](https://github.com/sonata-project/SonataUserBundle/pull/1643)] Support for `sonata-project/form-extensions` 2.0 ([@jordisala1991](https://github.com/jordisala1991))

## [5.7.0](https://github.com/sonata-project/SonataUserBundle/compare/5.6.3...5.7.0) - 2023-04-24
### Removed
- [[#1632](https://github.com/sonata-project/SonataUserBundle/pull/1632)] Support for Symfony 4.4 ([@jordisala1991](https://github.com/jordisala1991))
- [[#1632](https://github.com/sonata-project/SonataUserBundle/pull/1632)] Support for Twig 2 ([@jordisala1991](https://github.com/jordisala1991))

## [5.6.3](https://github.com/sonata-project/SonataUserBundle/compare/5.6.2...5.6.3) - 2023-04-12
### Fixed
- [[#1628](https://github.com/sonata-project/SonataUserBundle/pull/1628)] ORM schema mapping does not give errors about validation root ([@jordisala1991](https://github.com/jordisala1991))
- [[#1626](https://github.com/sonata-project/SonataUserBundle/pull/1626)] Validation for unique `username` and `email` is applied to `usernameCanonical` and `emailCanonical` fields ([@jordisala1991](https://github.com/jordisala1991))
- [[#1626](https://github.com/sonata-project/SonataUserBundle/pull/1626)] Validation for MongoDB persistence ([@jordisala1991](https://github.com/jordisala1991))
- [[#1627](https://github.com/sonata-project/SonataUserBundle/pull/1627)] Avoid deprecation on the check for is impersonator granted on Symfony >= 5.4 ([@jordisala1991](https://github.com/jordisala1991))

## [5.6.2](https://github.com/sonata-project/SonataUserBundle/compare/5.6.1...5.6.2) - 2023-04-03
### Fixed
- [[#1624](https://github.com/sonata-project/SonataUserBundle/pull/1624)] XML validation of MongoDB ODM mapping. ([@landure](https://github.com/landure))
- [[#1620](https://github.com/sonata-project/SonataUserBundle/pull/1620)] Generate the profile link only if the logged user has access to it (edit or show permissions) ([@jordisala1991](https://github.com/jordisala1991))
- [[#1621](https://github.com/sonata-project/SonataUserBundle/pull/1621)] Ensure password is updated when it is the only field edited ([@jordisala1991](https://github.com/jordisala1991))
- [[#1621](https://github.com/sonata-project/SonataUserBundle/pull/1621)] Register doctrine event subscribers both for ORM and ODM ([@jordisala1991](https://github.com/jordisala1991))

## [5.6.1](https://github.com/sonata-project/SonataUserBundle/compare/5.6.0...5.6.1) - 2023-03-30
### Fixed
- [[#1615](https://github.com/sonata-project/SonataUserBundle/pull/1615)] Fix xml validation of MongoDB ODM mapping. ([@jordisala1991](https://github.com/jordisala1991))
- [[#1615](https://github.com/sonata-project/SonataUserBundle/pull/1615)] Avoid deprecation for enabled boolean field on MongoDB ODM. ([@jordisala1991](https://github.com/jordisala1991))
- [[#1616](https://github.com/sonata-project/SonataUserBundle/pull/1616)] Fix always show edit button on user admin list page. ([@jordisala1991](https://github.com/jordisala1991))

## [5.6.0](https://github.com/sonata-project/SonataUserBundle/compare/5.5.0...5.6.0) - 2023-03-22
### Added
- [[#1599](https://github.com/sonata-project/SonataUserBundle/pull/1599)] Support for `doctrine/collections` 2 ([@gremo](https://github.com/gremo))

### Removed
- [[#1606](https://github.com/sonata-project/SonataUserBundle/pull/1606)] Support for `doctrine/persistence` 2.x ([@jordisala1991](https://github.com/jordisala1991))
- [[#1596](https://github.com/sonata-project/SonataUserBundle/pull/1596)] Support for PHP 7.4 ([@SonataCI](https://github.com/SonataCI))
- [[#1596](https://github.com/sonata-project/SonataUserBundle/pull/1596)] Support for Symfony 6.0 and 6.1 ([@SonataCI](https://github.com/SonataCI))

## [5.5.0](https://github.com/sonata-project/SonataUserBundle/compare/5.4.0...5.5.0) - 2023-02-06
### Changed
- [[#1587](https://github.com/sonata-project/SonataUserBundle/pull/1587)] RolesBuilder: add attributes "admin_code", "group_label", "group_code". ([@piddubnij](https://github.com/piddubnij))

### Fixed
- [[#1587](https://github.com/sonata-project/SonataUserBundle/pull/1587)] Fix RolesBuilder attribute "admin_label" set admin translation domain ([@piddubnij](https://github.com/piddubnij))
- [[#1587](https://github.com/sonata-project/SonataUserBundle/pull/1587)] Fix roles matrix attribute "admin_label" can repeat not even in different groups but also in the same group ([@piddubnij](https://github.com/piddubnij))
- [[#1577](https://github.com/sonata-project/SonataUserBundle/pull/1577)] Sonata-admin 4.20 deprecation ([@VincentLanglet](https://github.com/VincentLanglet))

## [5.4.0](https://github.com/sonata-project/SonataUserBundle/compare/5.3.3...5.4.0) - 2022-10-09
### Changed
- [[#1572](https://github.com/sonata-project/SonataUserBundle/pull/1572)] Password field is now of type PasswordType (was TextType). ([@m-ar-c](https://github.com/m-ar-c))

### Fixed
- [[#1567](https://github.com/sonata-project/SonataUserBundle/pull/1567)] Add UniqueEntity constraint to username field in order to prevent UniqueConstraintViolationException exception. ([@m-ar-c](https://github.com/m-ar-c))

## [5.3.3](https://github.com/sonata-project/SonataUserBundle/compare/5.3.2...5.3.3) - 2022-08-13
### Added
- [[#1560](https://github.com/sonata-project/SonataUserBundle/pull/1560)] Add support for `sonata-project/doctrine-extensions` ^2.0. ([@jordisala1991](https://github.com/jordisala1991))
- [[#1560](https://github.com/sonata-project/SonataUserBundle/pull/1560)] Add support for `sonata-project/twig-extensions` ^2.0. ([@jordisala1991](https://github.com/jordisala1991))

### Fixed
- [[#1562](https://github.com/sonata-project/SonataUserBundle/pull/1562)] Add UniqueEntity constraint to email field in order to prevent UniqueConstraintViolationException exception. ([@m-ar-c](https://github.com/m-ar-c))

## [5.3.2](https://github.com/sonata-project/SonataUserBundle/compare/5.3.1...5.3.2) - 2022-07-27
### Fixed
- [[#1556](https://github.com/sonata-project/SonataUserBundle/pull/1556)] `User::getUsername` return the real value in database (and can return null). ([@VincentLanglet](https://github.com/VincentLanglet))

## [5.3.1](https://github.com/sonata-project/SonataUserBundle/compare/5.3.0...5.3.1) - 2022-07-20
### Fixed
- [[#1553](https://github.com/sonata-project/SonataUserBundle/pull/1553)] Fix user edit page to check for a form attribute before rendering the role matrix. ([@wluijt-endertech](https://github.com/wluijt-endertech))

## [5.3.0](https://github.com/sonata-project/SonataUserBundle/compare/5.2.0...5.3.0) - 2022-07-12
### Added
- [[#1540](https://github.com/sonata-project/SonataUserBundle/pull/1540)] Added `RoleMatrixType:exclude` Option to hide choices. ([@Hanmac](https://github.com/Hanmac))

## [5.2.0](https://github.com/sonata-project/SonataUserBundle/compare/5.1.0...5.2.0) - 2022-06-25
### Added
- [[#1543](https://github.com/sonata-project/SonataUserBundle/pull/1543)] Add compatibility with `doctrine/persistence` ^3.0. ([@jordisala1991](https://github.com/jordisala1991))

### Removed
- [[#1543](https://github.com/sonata-project/SonataUserBundle/pull/1543)] Avoid deprecations with lazy commands on Symfony 6.1. ([@jordisala1991](https://github.com/jordisala1991))
- [[#1541](https://github.com/sonata-project/SonataUserBundle/pull/1541)] Support of Symfony 5.3 ([@franmomu](https://github.com/franmomu))

## [5.1.0](https://github.com/sonata-project/SonataUserBundle/compare/5.0.0...5.1.0) - 2022-04-29
### Changed
- [[#1533](https://github.com/sonata-project/SonataUserBundle/pull/1533)] Changed UserAdmin classnameLabel. This affects the breadcrumb translation generation. With this change Sonata will always pick the SonataUserBundle breadcrumbs translations by default. ([@aerrasti](https://github.com/aerrasti))

## [5.0.0](https://github.com/sonata-project/SonataUserBundle/compare/5.0.0-rc.1...5.0.0) - 2022-03-13
### Fixed
- [[#1517](https://github.com/sonata-project/SonataUserBundle/pull/1517)] Added validation translation to fix untranslated content. ([@jordisala1991](https://github.com/jordisala1991))
- [[#1515](https://github.com/sonata-project/SonataUserBundle/pull/1515)] Fixed remember me checkbox padding on login page. ([@jordisala1991](https://github.com/jordisala1991))

## [5.0.0-rc-1](https://github.com/sonata-project/SonataUserBundle/compare/5.0.0-alpha-1...5.0.0-rc-1) - 2022-03-01
### Added
- [[#1495](https://github.com/sonata-project/SonataUserBundle/pull/1495)] Added support for `symfony/translation-contracts` ^3.0. ([@jordisala1991](https://github.com/jordisala1991))

### Changed
- [[#1503](https://github.com/sonata-project/SonataUserBundle/pull/1503)] SonataAdminBundle dependency is now optional. Trying to access `sonata_user.userAdmin` variable in Twig templates will throw a `\LogicException` if SonataAdminBundle is not installed. ([@supersmile2009](https://github.com/supersmile2009))

### Fixed
- [[#1508](https://github.com/sonata-project/SonataUserBundle/pull/1508)] Fixed show translations on User admin ([@jordisala1991](https://github.com/jordisala1991))
- [[#1500](https://github.com/sonata-project/SonataUserBundle/pull/1500)] Simplified User admin to show all fields in a single page. ([@jordisala1991](https://github.com/jordisala1991))
- [[#1500](https://github.com/sonata-project/SonataUserBundle/pull/1500)] Fixed User admin translation for group fields. ([@jordisala1991](https://github.com/jordisala1991))

### Removed
- [[#1500](https://github.com/sonata-project/SonataUserBundle/pull/1500)] Removed `SecurityRolesType` to render roles on the User edit form. ([@jordisala1991](https://github.com/jordisala1991))

## [5.0.0-alpha-1](https://github.com/sonata-project/SonataUserBundle/compare/4.x...5.0.0-alpha-1) - 2022-02-05
### Added
- [[#1489](https://github.com/sonata-project/SonataUserBundle/pull/1489)] Added `ResetPasswordRequestFormType` form type. ([@jordisala1991](https://github.com/jordisala1991))
- [[#1484](https://github.com/sonata-project/SonataUserBundle/pull/1484)] Added `sonata:user:create` command from FOSUserBundle ([@jordisala1991](https://github.com/jordisala1991))
- [[#1484](https://github.com/sonata-project/SonataUserBundle/pull/1484)] Added `sonata:user:promote` command from FOSUserBundle ([@jordisala1991](https://github.com/jordisala1991))
- [[#1484](https://github.com/sonata-project/SonataUserBundle/pull/1484)] Added `sonata:user:demote` command from FOSUserBundle ([@jordisala1991](https://github.com/jordisala1991))
- [[#1484](https://github.com/sonata-project/SonataUserBundle/pull/1484)] Added `sonata:user:change-password` command from FOSUserBundle ([@jordisala1991](https://github.com/jordisala1991))
- [[#1484](https://github.com/sonata-project/SonataUserBundle/pull/1484)] Added `sonata:user:activate` command from FOSUserBundle ([@jordisala1991](https://github.com/jordisala1991))
- [[#1484](https://github.com/sonata-project/SonataUserBundle/pull/1484)] Added `sonata:user:deactivate` command from FOSUserBundle ([@jordisala1991](https://github.com/jordisala1991))
- [[#1479](https://github.com/sonata-project/SonataUserBundle/pull/1479)] Added `EditableRolesBuilderInterface`. ([@jordisala1991](https://github.com/jordisala1991))
- [[#1454](https://github.com/sonata-project/SonataUserBundle/pull/1454)] Added support for Symfony 5. ([@jordisala1991](https://github.com/jordisala1991))
- [[#1454](https://github.com/sonata-project/SonataUserBundle/pull/1454)] Added support for Symfony 6. ([@jordisala1991](https://github.com/jordisala1991))
- [[#1444](https://github.com/sonata-project/SonataUserBundle/pull/1444)] Added support for Sonata 4. ([@jordisala1991](https://github.com/jordisala1991))

### Changed
- [[#1479](https://github.com/sonata-project/SonataUserBundle/pull/1479)] Changed `__construct()` of `EditableRolesBuilder` to provide `TranslatorInterface` ([@jordisala1991](https://github.com/jordisala1991))

### Fixed
- [[#1485](https://github.com/sonata-project/SonataUserBundle/pull/1485)] Fixed icons usage for Font Awesome 5. ([@jordisala1991](https://github.com/jordisala1991))
- [[#1481](https://github.com/sonata-project/SonataUserBundle/pull/1481)] Improve bundle configuration for default and required parameters. ([@jordisala1991](https://github.com/jordisala1991))

### Removed
- [[#1490](https://github.com/sonata-project/SonataUserBundle/pull/1490)] Removed usage of container parameters for most of the bundle configuration. ([@jordisala1991](https://github.com/jordisala1991))
- [[#1468](https://github.com/sonata-project/SonataUserBundle/pull/1468)] Removed user groups feature. ([@jordisala1991](https://github.com/jordisala1991))
- [[#1463](https://github.com/sonata-project/SonataUserBundle/pull/1463)] Removed support for PHP 7.3 ([@jordisala1991](https://github.com/jordisala1991))
- [[#1462](https://github.com/sonata-project/SonataUserBundle/pull/1462)] Removed social and profile fields from User. ([@jordisala1991](https://github.com/jordisala1991))
- [[#1449](https://github.com/sonata-project/SonataUserBundle/pull/1449)] Removed FOSUserBundle dependency. ([@jordisala1991](https://github.com/jordisala1991))

## [4.15.0](https://github.com/sonata-project/SonataUserBundle/compare/4.14.0...4.15.0) - 2022-01-08
### Deprecated
- [[#1471](https://github.com/sonata-project/SonataUserBundle/pull/1471)] Deprecated extending most of the classes of this bundle. ([@jordisala1991](https://github.com/jordisala1991))

### Fixed
- [[#1476](https://github.com/sonata-project/SonataUserBundle/pull/1476)] `User::__serialize()` and `User::__unserialize()` methods ([@VincentLanglet](https://github.com/VincentLanglet))

## [4.14.0](https://github.com/sonata-project/SonataUserBundle/compare/4.13.0...4.14.0) - 2021-12-28
### Deprecated
- [[#1446](https://github.com/sonata-project/SonataUserBundle/pull/1446)] Deprecated the integration with `sonata-project/GoogleAuthenticator` ([@jordisala1991](https://github.com/jordisala1991))
- [[#1439](https://github.com/sonata-project/SonataUserBundle/pull/1439)] Deprecated ReST API with FOSRest, Nelmio Api Docs and JMS Serializer. ([@jordisala1991](https://github.com/jordisala1991))

### Fixed
- [[#1430](https://github.com/sonata-project/SonataUserBundle/pull/1430)] Taking into account sonata_admin.options.logo_content configuration value ([@AntoineRoue](https://github.com/AntoineRoue))

## [4.13.0](https://github.com/sonata-project/SonataUserBundle/compare/4.12.0...4.13.0) - 2021-11-04
### Fixed
- [[#1412](https://github.com/sonata-project/SonataUserBundle/pull/1412)] `en`, `lt`, and `ru` translation keys ([@ft-1408](https://github.com/ft-1408))

### Removed
- [[#1426](https://github.com/sonata-project/SonataUserBundle/pull/1426)] Conflict with `nelmio/api-doc-bundle` v4 ([@wbloszyk](https://github.com/wbloszyk))

## [4.12.0](https://github.com/sonata-project/SonataUserBundle/compare/4.11.1...4.12.0) - 2021-09-08
### Added
- [[#1396](https://github.com/sonata-project/SonataUserBundle/pull/1396)] Added support for PHP 8.0 ([@jordisala1991](https://github.com/jordisala1991))
- [[#1396](https://github.com/sonata-project/SonataUserBundle/pull/1396)] Added support for Twig 3 ([@jordisala1991](https://github.com/jordisala1991))
- [[#1396](https://github.com/sonata-project/SonataUserBundle/pull/1396)] Added support for Symfony `http-foundation` 5.3 ([@jordisala1991](https://github.com/jordisala1991))

### Removed
- [[#1396](https://github.com/sonata-project/SonataUserBundle/pull/1396)] Removed support for Symfony 5.1 and 5.2 ([@jordisala1991](https://github.com/jordisala1991))

## [4.11.1](https://github.com/sonata-project/SonataUserBundle/compare/4.11.0...4.11.1) - 2021-03-03
### Fixed
- [[#1332](https://github.com/sonata-project/SonataUserBundle/pull/1332)] Fix compatibility with doctrine/persistence 2.0 ([@jordisala1991](https://github.com/jordisala1991))

## [4.11.0](https://github.com/sonata-project/SonataUserBundle/compare/4.10.2...4.11.0) - 2021-02-15
### Added
- [[#1325](https://github.com/sonata-project/SonataUserBundle/pull/1325)] Support for "doctrine/common:^3". ([@phansys](https://github.com/phansys))

## [4.10.2](https://github.com/sonata-project/SonataUserBundle/compare/4.10.1...4.10.2) - 2021-02-05
### Changed
- [[#1323](https://github.com/sonata-project/SonataUserBundle/pull/1323)] `GroupManager::getPager()` return type is `Sonata\DatagridBundle\Pager\PagerInterface`. ([@Jean-ita](https://github.com/Jean-ita))
- [[#1323](https://github.com/sonata-project/SonataUserBundle/pull/1323)] `UserManager::getPager()` return type is `Sonata\DatagridBundle\Pager\PagerInterface`. ([@Jean-ita](https://github.com/Jean-ita))

## [4.10.1](https://github.com/sonata-project/SonataUserBundle/compare/4.10.0...4.10.1) - 2020-11-24
### Fixed
- [[#1288](https://github.com/sonata-project/SonataUserBundle/pull/1288)] Duplicate translation in `src/Resources/translations/SonataUserBundle.nl.xliff` ([@elyanory](https://github.com/elyanory))

## [4.10.0](https://github.com/sonata-project/SonataUserBundle/compare/4.9.0...4.10.0) - 2020-11-24
### Added
- [[#1263](https://github.com/sonata-project/SonataUserBundle/pull/1263)] Support for "symfony/mailer" in `Sonata\UserBundle\Mailer\Mailer` ([@phansys](https://github.com/phansys))

### Changed
- [[#1271](https://github.com/sonata-project/SonataUserBundle/pull/1271)] Updates dutch translations ([@zghosts](https://github.com/zghosts))

### Deprecated
- [[#1263](https://github.com/sonata-project/SonataUserBundle/pull/1263)] Support for "swiftmailer/swiftmailer" in `Sonata\UserBundle\Mailer\Mailer` ([@phansys](https://github.com/phansys))

### Fixed
- [[#1266](https://github.com/sonata-project/SonataUserBundle/pull/1266)] Fixed the problem of the message "sonata_user_already_authenticated" not being translated ([@BitScout](https://github.com/BitScout))

## [4.9.0](https://github.com/sonata-project/SonataUserBundle/compare/4.8.0...4.9.0) - 2020-10-26
### Added
- [[#1225](https://github.com/sonata-project/SonataUserBundle/pull/1225)] Support for `nelmio/api-doc-bundle` >= 3.6 ([@wbloszyk](https://github.com/wbloszyk))
- [[#1230](https://github.com/sonata-project/SonataUserBundle/pull/1230)] Support for sonata-project/datagrid-bundle to version ^3.0 ([@wbloszyk](https://github.com/wbloszyk))

### Fixed
- [[#1247](https://github.com/sonata-project/SonataUserBundle/pull/1247)] Fixed Asserts validation for Default group. ([@eerison](https://github.com/eerison))
- [[#1254](https://github.com/sonata-project/SonataUserBundle/pull/1254)] Deprecation about the extension of `AbstractAdmin::getExportFields()` method in `UserAdmin`. ([@phansys](https://github.com/phansys))
- [[#1235](https://github.com/sonata-project/SonataUserBundle/pull/1235)] API routing paths after move routing type from 'REST' to 'XML' in v4.6.0 ([@wbloszyk](https://github.com/wbloszyk))
- [[#1212](https://github.com/sonata-project/SonataUserBundle/pull/1212)] Removed sonata.templating argument from google_authenticator.xml ([@Jean-ita](https://github.com/Jean-ita))

### Removed
- [[#1230](https://github.com/sonata-project/SonataUserBundle/pull/1230)] Support for sonata-project/datagrid-bundle to version < 3.0 ([@wbloszyk](https://github.com/wbloszyk))

## [4.8.0](https://github.com/sonata-project/SonataUserBundle/compare/4.7.0...4.8.0) - 2020-09-07
### Added
- [[#1210](https://github.com/sonata-project/SonataUserBundle/pull/1210)] Added support for symfony/options-resolver:^5.1 ([@phansys](https://github.com/phansys))

### Changed
- [[#1205](https://github.com/sonata-project/SonataUserBundle/pull/1205)] Replace `ip_white_list` by `trusted_ip_list` configuration key ([@davidromani](https://github.com/davidromani))

### Fixed
- [[#1198](https://github.com/sonata-project/SonataUserBundle/pull/1198)] Fixed support for string model identifiers at Open API definitions ([@phansys](https://github.com/phansys))

### Removed
- [[#1198](https://github.com/sonata-project/SonataUserBundle/pull/1198)] Removed requirements that were only allowing integers for model identifiers at Open API definitions ([@phansys](https://github.com/phansys))

## [4.7.0](https://github.com/sonata-project/SonataUserBundle/compare/4.6.0...4.7.0) - 2020-07-30
### Changed
- [[#1183](https://github.com/sonata-project/SonataUserBundle/pull/1183)]
  SonataEasyExtendsBundle is now optional, using SonataDoctrineBundle is
preferred ([@jordisala1991](https://github.com/jordisala1991))

### Deprecated
- [[#1183](https://github.com/sonata-project/SonataUserBundle/pull/1183)] Using
  SonataEasyExtendsBundle to add Doctrine mapping information
([@jordisala1991](https://github.com/jordisala1991))

### Fixed
- [[#1202](https://github.com/sonata-project/SonataUserBundle/pull/1202)] Fixed
  wrong placeholder delimiters at ReST API controller `UserController`.
([@phansys](https://github.com/phansys))
- [[#1195](https://github.com/sonata-project/SonataUserBundle/pull/1195)] Fixed
  passing "format' parameter to API routes.
([@phansys](https://github.com/phansys))
- [[#1194](https://github.com/sonata-project/SonataUserBundle/pull/1194)] Fixed
  API route names. ([@phansys](https://github.com/phansys))

### Removed
- [[#1192](https://github.com/sonata-project/SonataUserBundle/pull/1192)] Support for PHP < 7.2 ([@wbloszyk](https://github.com/wbloszyk))

## [4.6.0](https://github.com/sonata-project/SonataUserBundle/compare/4.5.3...4.6.0) - 2020-06-29
### Added
- [[#1185](https://github.com/sonata-project/SonataUserBundle/pull/1185)] Added
  support for "friendsofsymfony/rest-bundle:^3.0";
([@phansys](https://github.com/phansys))
- [[#1185](https://github.com/sonata-project/SonataUserBundle/pull/1185)] Added
  public alias `Sonata\UserBundle\Controller\Api\UserController` for
`sonata.user.controller.api.user` service;
([@phansys](https://github.com/phansys))
- [[#1185](https://github.com/sonata-project/SonataUserBundle/pull/1185)] Added
  public alias `Sonata\UserBundle\Controller\Api\GroupController` for
`sonata.user.controller.api.group` service.
([@phansys](https://github.com/phansys))

### Fixed
- [[#1180](https://github.com/sonata-project/SonataUserBundle/pull/1180)] Fixed
  display errors in reset template
([@clementlefrancois](https://github.com/clementlefrancois))
- [[#1175](https://github.com/sonata-project/SonataUserBundle/pull/1175)] Fixed
  a bug with ResetAction relying on concrete Session implementation
([@oleg-andreyev](https://github.com/oleg-andreyev))

### Removed
- [[#1185](https://github.com/sonata-project/SonataUserBundle/pull/1185)]
  Removed support for `symfony/*`:<4.4;
([@phansys](https://github.com/phansys))
- [[#1185](https://github.com/sonata-project/SonataUserBundle/pull/1185)]
  Removed support for deprecated "rest" routing type.
([@phansys](https://github.com/phansys))
- [[#1173](https://github.com/sonata-project/SonataUserBundle/pull/1173)]
  Remove SonataCoreBundle dependencies
([@wbloszyk](https://github.com/wbloszyk))

## [4.5.3](https://github.com/sonata-project/SonataUserBundle/compare/4.5.2...4.5.3) - 2020-05-07
### Added
- czech translations

### Fixed
- Fixed retrieving all forwarded ip headers in the google authenticator helper

### Security
- Avoid leaking usernames in password recovery

## [4.5.2](https://github.com/sonata-project/SonataUserBundle/compare/4.5.1...4.5.2) - 2020-02-04
### Fixed
- some deprecations form the core bundle
- Doctrine MongoDB mapping

## [4.5.1](https://github.com/sonata-project/SonataUserBundle/compare/4.5.0...4.5.1) - 2019-10-21
### Added
- Add missing translation for admin menu

### Fixed
 - Fixed invocation of non-existent "getLogger" method. Changed to access property.

## [4.5.0](https://github.com/sonata-project/SonataUserBundle/compare/4.4.0...4.5.0) - 2019-09-27
### Fixed
- Missing Spanish translations.

### Changed
- Replaced usages of deprecated "templating" service with "twig" where possible.

### Removed
- Dependency conflict against "jms/serializer:^3.0".

### Changed
- Add missing dependency against "twig/twig"
- Changed usages of `{% spaceless %}` tag, which is deprecated as of Twig 1.38
  with `{% apply spaceless %}` filter

## [4.4.0](https://github.com/sonata-project/SonataUserBundle/compare/4.3.0...4.4.0) - 2019-06-13

### Added
- Added compatibility with jms/serializer-bundle:^3.0 and jms/serializer:^2.0
- Added missing google auth french translation

### Fixed
- Fixed a bug with inability to use external model classes
- Fixed wrong google auth user manager argument when not using autowiring
- Fixed hard coded google auth redirection url

### Changed
- Updated `_controller` attribute for routes which were using deprecated syntax.

## [4.3.0](https://github.com/sonata-project/SonataUserBundle/compare/4.2.3...4.3.0) - 2019-01-30

### Fixed
- exception when calling `sonata:user:two-step-verification` even with proper configuration
- deprecation for symfony/config 4.2+
- case when user was not found in sendEmailAction
- rendering resetting email on Windows

### Deprecated
- executing a `Sonata\UserBundle\Command\TwoStepVerificationCommand` that did not receive arguments.

## [4.2.3](https://github.com/sonata-project/SonataUserBundle/compare/4.2.2...4.2.3) - 2018-07-08

### Fixed
Replace deprecated use of Google\Authenticator\GoogleAuthenticator by Sonata's namespace

## [4.2.2](https://github.com/sonata-project/SonataUserBundle/compare/4.2.1...4.2.2) - 2018-06-09

### Fixed

- The following services can be get via the container again:
  - `sonata.user.user_manager`
  - `sonata.user.group_manager`
  - `Sonata\UserBundle\Form\Type\RolesMatrixType`
  - `Sonata\UserBundle\Entity\UserManagerProxy`

## [4.2.1](https://github.com/sonata-project/SonataUserBundle/compare/4.2.0...4.2.1) - 2018-05-15

### Fixed

- Load `Sonata\UserBundle\Twig\RolesMatrixExtension` only if UserBundle is used
- Make `Sonata\UserBundle\Form\Type\SecurityRolesType` public, as it is lazy loaded
- Move static `Entity\BaseUser::getGenderList` to `Model\User` so that it is available to all persistence systems

## [4.2.0](https://github.com/sonata-project/SonataUserBundle/compare/4.1.1...4.2.0) - 2018-05-08

### Added
- Role permissions can now be displayed in a matrix view using the `Sonata\UserBundle\Form\Type\RolesMatrixType`

### Fixed
- Missing french translations were added
- Commands now work on Symfony 4

### Removed
- Removed compatibility with older versions of FOSRestBundle (<2.1)

## [4.1.1](https://github.com/sonata-project/SonataUserBundle/compare/4.1.0...4.1.1) - 2018-02-08
### Changed
- Switch all templates references to Twig namespaced syntax
- Switch from templating service to sonata.templating

### Fixed
- choices for User gender now appears correctly flipped and translated
- Deprecation message on SecurityRolesType about `choices_as_values`

## [4.1.0](https://github.com/sonata-project/SonataUserBundle/compare/4.0.1...4.1.0) - 2018-01-20
### Added
- New experience for `sonata-project/google-authenticator` users. Showing QR-code in login form, automatically setting 2FA secret to user.
- use forcedRoles and ipWhiteList also on InteractiveLoginListener

### Changed
- use `symfony/security-core` and `symfony/security-acl` instead of `symfony/security`

### Fixed
- Avoid templates path colon notation
- Bad conflict rule for nelmio/api-doc-bundle

## [4.0.1](https://github.com/sonata-project/SonataUserBundle/compare/4.0.0...4.0.1) - 2017-12-20
### Fixed
- Fixed flipped choices values/labels in SecurityRolesType when using symfony 2.8
- Admin pool variable in admin resetting templates

## [4.0.0](https://github.com/sonata-project/SonataUserBundle/compare/3.6.0...4.0.0) - 2017-12-04
### Added
- Add support for FOSUser 2.0
- Added missing `swiftmailer` dependency

### Changed
- Risky code change for PHP 7
- Made `sonata-project/google-authenticator` an optional dependency
- Moved public methods of `User` to `UserInterface`
- Lowered upper composer dependencies
- default values moved to the Configuration class
- `sonata.user.admin.user` and `sonata.user.admin.group` are public now

### Fixed
- Fixed PHPDoc
- Fixed wrong parent calls in UserManager
- Fixed wrong router call in Controller
- missing logo in templates

### Removed
- Removed removed user model properties
- Removed deprecated code

## [3.6.0](https://github.com/sonata-project/SonataUserBundle/compare/3.5.0...3.6.0) - 2017-12-04
### Added
- make Roles in SecurityRolesType translateable
- Added Russian translations

### Changed
- Changed internal folder structure to `src`, `tests` and `docs`

### Fixed
- Added a check to the UserAclVoter class to ensure the subject is an object

## [3.5.0](https://github.com/sonata-project/SonataUserBundle/compare/3.4.0...3.5.0) - 2017-11-04
### Changed
- Rollback to PHP 5.6 as minimum support.

## [3.4.0](https://github.com/sonata-project/SonataUserBundle/compare/3.3.0...3.4.0) - 2017-10-22
### Fixed
- Fixed the twig configuration setting bug.

### Removed
- Support for old versions of php and Symfony.

## [3.3.0](https://github.com/sonata-project/SonataUserBundle/compare/3.2.4...3.3.0) - 2017-10-22
### Changed
- Use sonata admin pool the get the master role name

### Fixed
- `AccountBlockService` extends `AbstractAdminBlockService` instead of wrong `AbstractBlockService`
- missing spanish translations were added

## [3.2.4](https://github.com/sonata-project/SonataUserBundle/compare/3.2.3...3.2.4) - 2017-06-14
### Added
- Added Dutch translation for `title_user_authentication`

### Changed
- Google Authenticator 2 is now allowed

### Fixed
- Deprecated block service usage
- Compatibility with Twig 2.0 was improved
- Fixed hardcoded paths to classes in `.xml.skeleton` files of config

## [3.2.3](https://github.com/sonata-project/SonataUserBundle/compare/3.2.2...3.2.3) - 2017-03-16
### Fixed
- Fix non-use of container for has/get services.

## [3.2.2](https://github.com/sonata-project/SonataUserBundle/compare/3.2.1...3.2.2) - 2017-03-08
### Fixed
- Fixed empty route after registration
- Added missing throw in change password process
- Avoid deprecation message by using request_stack when it is present
- Avoid deprecation message by changing CSRF token generation when possible
- Wrong factory definition

### Removed
- Removed form types non FQCN on SF2.8+
- Removed deprecations about form factory on SF2.8

## [3.2.1](https://github.com/sonata-project/SonataUserBundle/compare/3.2.0...3.2.1) - 2017-02-09
### Added
- Add missing (optional) dependency for JMSSerializerBundle, needed for the services defined in serializer.xml and api_form.xml

### Fixed
- FOSRestBundle 2.x was improved
- fixed a cross dependency when using UserBundle with FOSRestBundle and NelmioApiDocBundle depending JMSSerializerBundle.
- add missing use for DependencyInjection\Reference
- Added $ sign that was missing from a previous refactoring
- `asset` return `/` if default avatar is empty
- Declaration of `UserManagerProxy` uses `Sonata\UserBundle\Entity\User` instead of `%fos_user.model.user.class%`
- the bundle can be used without a `sonata.user.editable_role_builder` service
- Issue where service was injected to constructor at wrong position
- Missing italian translations
- Deprecation of `security.context` on `AdminSecurityController`

## [3.2.0](https://github.com/sonata-project/SonataUserBundle/compare/3.1.0...3.2.0) - 2016-11-25
### Added
- Added russian and ukrainian translations

### Deprecated
- Relying on the `sonata_basket_delivery_redirect` is deprecated and won't be supported anymore

### Fixed
- The reset password url now points to the action dedicated to administrators again

### Removed
- The conflict rule for FOSRestBundle `>=2.0`

## [3.1.0](https://github.com/sonata-project/SonataUserBundle/compare/3.0.1...3.1.0) - 2016-10-14
### Changed
- The `friendsofsymfony/rest-bundle` dependency is optional again
- The `jms/serializer-bundle` dependency is optional again
- The `nelmio/api-doc-bundle` dependency is optional again
- Changed implementation of `SecurityFOSUser1Controller::loginAction`
- Changed implementation of `AdminSecurityController::loginAction`
- Changed how the error message is translated in `login.html.twig`
- Changed how the error message is translated in `base_login.html.twig`

### Fixed
- Fixed a potential null error in `SecurityFOSUser1Controller::loginAction`
- Fixed a potential empty route after calling `RegistrationFOSUser1Controller::registerAction`
- Fixed wrong route name "sonata_user_admin_resetting_request", replaced with "sonata_user_resetting_request"
- Symfony 3 security classes use in `AdminSecurityController`
- Fixed a possible security risk as noticed in this [line](https://github.com/sonata-project/SonataUserBundle/blob/88a962818dd6218379ff1439183a15647837bda0/Controller/AdminSecurityController.php#L40)

### Removed
- Internal test classes are now excluded from the autoloader
- Removed translation for 'Bad credentials' message in `SonataUserBundle.de.xliff`

## [3.0.1](https://github.com/sonata-project/SonataUserBundle/compare/3.0.0...3.0.1) - 2016-05-20
### Changed
- Admin classes extend `Sonata\AdminBundle\Admin\AbstractAdmin`
