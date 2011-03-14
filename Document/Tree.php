<?php

namespace FOS\CommentBundle\Document;

class Tree
{
    private $comment;
    private $children = array();

    public function __construct(Comment $comment = null)
    {
        $this->comment = $comment;
    }

    public function add(Comment $comment)
    {
        $this->children[$comment->getId()] = new Tree($comment);
    }

    public function traverse($id)
    {
        return $this->children[$id];
    }

    public function toArray()
    {
        $children = array();
        foreach ($this->children as $child) {
            array_unshift($children, $child->toArray());
        }

        return $this->comment ? array('comment' => $this->comment, 'children' => $children) : $children;
    }
}
