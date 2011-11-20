<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Tests\Creator;

use FOS\CommentBundle\Creator\DefaultVoteCreator;

/**
 * Tests the functionality provided by Creator\DefaultThreadCreator.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class DefaultVoteCreatorTest extends \PHPUnit_Framework_TestCase
{
    protected $comment;
    protected $validator;
    protected $vote;
    protected $voteBlamer;
    protected $voteManager;

    public function setUp()
    {
        $this->comment = $this->getMock('FOS\CommentBundle\Model\VotableCommentInterface');
        $this->validator = $this->getMock('Symfony\Component\Validator\ValidatorInterface');
        $this->vote = $this->getMock('FOS\CommentBundle\Model\VoteInterface');
        $this->voteBlamer = $this->getMock('FOS\CommentBundle\Blamer\VoteBlamerInterface');
        $this->voteManager = $this->getMock('FOS\CommentBundle\Model\VoteManagerInterface');
    }

    public function testCreateInvalid()
    {
        $this->validator->expects($this->once())
            ->method('validate')
            ->will($this->returnValue(array('error' => 'Has an error')));

        $creator = new DefaultVoteCreator($this->voteManager, $this->voteBlamer, $this->validator);
        $result = $creator->create($this->vote, $this->comment);

        $this->assertFalse($result);
    }

    public function testCreate()
    {
        $this->voteBlamer->expects($this->once())
            ->method('blame')
            ->with($this->vote);

        $this->validator->expects($this->once())
            ->method('validate')
            ->will($this->returnValue(array()));

        $this->voteManager->expects($this->once())
            ->method('addVote')
            ->with($this->vote, $this->comment);

        $creator = new DefaultVoteCreator($this->voteManager, $this->voteBlamer, $this->validator);
        $result = $creator->create($this->vote, $this->comment);

        $this->assertTrue($result);
    }

}