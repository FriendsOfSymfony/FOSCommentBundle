<?php

namespace FOS\CommentBundle\Model;

class Tree
{
    private $comment;
    private $children = array();

    public function __construct(CommentInterface $comment = null)
    {
        $this->comment = $comment;
    }

    public function add(CommentInterface $comment)
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
        foreach ($this->children AS $child) {
            $children[] = $child->toArray();
        }

        return $this->comment ? array('comment' => $this->comment, 'children' => $children) : $children;
    }
}
