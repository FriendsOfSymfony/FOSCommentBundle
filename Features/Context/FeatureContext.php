<?php

namespace FOS\CommentBundle\Features\Context;

use Behat\BehatBundle\Context\MinkContext;
use Behat\Behat\Exception\PendingException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Feature context.
 */
class FeatureContext extends MinkContext
{
    /**
     * @Given /^I have a thread identified by "([^"]*)" with a link of "([^"]*)"$/
     */
    public function iHaveAThreadIdentifiedBy($id, $link)
    {
        $thread = $this->getThreadManager()->findThreadById($id);
        if (!$thread) {
            $thread = $this->getThreadManager()->createThread($id);
        }

        $thread->setPermalink($link);

        $this->getThreadManager()->saveThread($thread);
    }

    /**
     * @return \FOS\CommentBundle\Model\ThreadManagerInterface
     */
    private function getThreadManager()
    {
        return $this->getContainer()->get('fos_comment.manager.thread');
    }
}