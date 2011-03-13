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
        $array = array('comment' => $this->comment, 'children' => array());;
        foreach ($this->children as $child) {
            $array['children'][] = $child->toArray();
        }
        if (!$this->comment) {
            $array = $array['children'];
        }

        return $array;
    }
}
