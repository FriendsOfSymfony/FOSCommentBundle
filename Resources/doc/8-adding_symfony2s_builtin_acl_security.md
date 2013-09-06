Step 8: Adding ACL security
======================================

**Note:**

> This bundle ships with support different security setups. You can also have a look at [Adding role based ACL security](7-adding_role_based_acl_security.md).

To use the built in Acl system, it must first be initialised with the Symfony2 console:

``` bash
$ app/console init:acl
```

Additionally, your configuration needs to be modified to add the right managers:

``` yaml
# app/config/config.yml

fos_comment:
    acl: true
    service:
        manager:
            thread:  fos_comment.manager.thread.acl
            comment: fos_comment.manager.comment.acl
            vote:    fos_comment.manager.vote.acl
```

**Note:**

> Note: you must enable the Security Acl component::

``` yaml
# app/config/security.yml
security:
    # ...
    acl:
        connection: default
```

Finally, you must populate the Acl system with entries that may not be there yet
by running:

``` bash
$ app/console fos:comment:installAces
```

This will make sure that the Acl entries in the database are correct. This command
must be run whenever any configuration for security changes in FOSCommentBundle,
including enabling the security features or changing the FQCN of your extended
FOSCommentBundle objects.

## That is it!
[Return to the index.](index.md)
