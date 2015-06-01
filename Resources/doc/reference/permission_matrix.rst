The matrix of permission
========================

Configuration
-------------

The type of form ``sonata_security_roles`` was improved to show quite the roles in matrix.
Indeed, for every admin defined in sonata admin we have six roles by default:

    - Edit
    - Create
    - List
    - Delete
    - Show
    - Export

I add two permissions:
    - Operator: you can manage this entity
    - Master: you can manage this entity and delegate permission


The matrix goes represented these permissions in table, as shows us this screen shot

![The premission's matrice](../images/premission_matrice.png)

You have two parts:
 - Six default permissions for each entity
 - Customes roles defined in the ``security.yml``

It's important to know, if you define a new permission in your ``security.yml``, it will be automatically add in the second part

How to use it
-------------

You need to use ``sonata_security_roles`` form type.
Add this line in stylesheets block:

```html
<link rel="stylesheet" href="{{ asset("bundles/sonatauser/css/security.css") }}"/>
```

Add this line in javascripts block:

```html
<script type="text/javascript" src="{{ asset('bundles/sonatauser/js/permission.js') }}"></script>
```
