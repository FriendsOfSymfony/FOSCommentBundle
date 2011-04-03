<?php

namespace FOS\CommentBundle\Model;

use FOS\CommentBundle\Sorting\SortingInterface;

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

    public function getComment()
    {
        return $this->comment;
    }

    public function traverse($id)
    {
        return $this->children[$id];
    }

    public function toArray(SortingInterface $sorter)
    {
        $children = array();
        foreach ($sorter->sort($this->children) AS $child) {
            $children[] = $child->toArray($sorter);
        }

        return $this->comment ? array('comment' => $this->comment, 'children' => $children) : $children;
    }
}
