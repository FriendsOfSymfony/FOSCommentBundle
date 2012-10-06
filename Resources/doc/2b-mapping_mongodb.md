Step 2b: Setup MongoDB mapping
==============================
The MongoDB implementation does not provide a concrete Comment class for your use,
you must create one:

``` php
<?php
// src/MyProject/MyBundle/Document/Comment.php

namespace MyProject\MyBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use FOS\CommentBundle\Document\Comment as BaseComment;

/**
 * @MongoDB\Document
 * @MongoDB\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class Comment extends BaseComment
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * Thread of this comment
     *
     * @var Thread
     * @MongoDB\ReferenceOne(targetDocument="MyProject\MyBundle\Document\Thread")
     */
    protected $thread;
}
```

Additionally, create the Thread class:

``` php
<?php
// src/MyProject/MyBundle/Document/Thread.php

namespace MyProject\MyBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use FOS\CommentBundle\Document\Thread as BaseThread;

/**
 * @MongoDB\Document
 * @MongoDB\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class Thread extends BaseThread
{

}
```

## Configure your application

In YAML:

``` yaml
# app/config/config.yml

fos_comment:
    db_driver: mongodb
    class:
        model:
            comment: MyProject\MyBundle\Document\Comment
            thread: MyProject\MyBundle\Document\Thread

assetic:
    bundles: [ "FOSCommentBundle" ]  
```

Or if you prefer XML:

``` xml
# app/config/config.xml

<fos_comment:config db-driver="mongodb">
    <fos_comment:class>
        <fos_comment:model
            comment="MyProject\MyBundle\Document\Comment"
            thread="MyProject\MyBundle\Document\Thread"
        />
    </fos_comment:class>
</fos_comment:config>
    
<assetic:config>
    <assetic:bundle name="FOSCommentBundle" />
</assetic:config>
```

### Back to the main step
[Step 2: Create your Comment and Thread classes](2-create_your_comment_and_thread_classes.md).
