Step 14: Mapping comment threads to entities
==============================================

Sometimes you want your comment thread to be part of another Entity, for example an Article entity.
You can now do this by adding the Commentable annotation into your entity.

### Setup Classes
To make this work, your entity needs to have a getEntityIdentifier method which might look like this.

    /**
     * Returns a string representation of the entity build out of BundleName + EntityName + EntityId
     *
     * @return string
     */
    public function getEntityIdentifier()
    {
        return 'AcmeArticleBundle:Article:' . $this->getId();
    }

After that, you need to add the comments property, and the getter and setter, to your entity.
 
    /**
     * @FOS\CommentBundle\Annotation\Commentable()
     */
    protected $comments;

    public function getComments() {
        return $this->comments;
    }

    public function setComments($comments) {
        $this->comments = $comments;

        return $this;
    }

### Updating the Database

To create the column in your database for this relation, run the doctrine update command

    php app/console doctrine:schema:update --force
    

### Integration with view
In your twig file, you can now load the comment section by using:

    {% include 'FOSCommentBundle:Thread:async.html.twig' with {'id': article.comments.id} %}
    
If you have dynamic objects, and you want to see if the entity is commentable, simply check if the comments are defined

    {% if object.comments is defined %}
        {% include 'FOSCommentBundle:Thread:async.html.twig' with {'id': object.comments.id} %}
    {% endif %}

### How it works
There is a event subscriber listening for the metadata load event, that adds the doctrine mapping dynamicly.
Then there is a Post load listener that checks of there's already a comment thread attached to it.
If that's not the case, it will generate a new thread.

## That is it!
[Return to the index.](index.md)
