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

    public function toArray($sortOrder = 'DESC')
    {
        $children = array();
        foreach ($this->children as $child) {
            if ($sortOrder == 'DESC') {
                array_unshift($children, $child->toArray());
            }
            else {
                $children[] = $child->toArray($sortOrder);
            }
        }

        return $this->comment ? array('comment' => $this->comment, 'children' => $children) : $children;
    }
}
