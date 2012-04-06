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
        $thread = $this->getThreadManager()->createThread($id);
        $thread->setPermalink($link);

        $this->getThreadManager()->saveThread($thread);
    }


    /**
     * @When /^I go to "([^"]*)"$/
     */
    public function iGoTo($url)
    {
        $this->visit($url);
    }

    /**
     * @When /^I go to "([^"]*)" as "([^"]*)" identified by "([^"]*)"$/
     */
    public function iGoToAsIdentifiedBy($url)
    {
        throw new PendingException;
    }

    /**
     * @Then /^I should see a thread with the identifier of "([^"]*)"$/
     */
    public function iShouldSeeAThreadWithTheIdentifierOf($argument1)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I should see a thread with the identifier to "([^"]*)"$/
     */
    public function iShouldSeeAThreadWithTheIdentifierTo($argument1)
    {
        throw new PendingException();
    }

    /**
     * @return \FOS\CommentBundle\Model\ThreadManagerInterface
     */
    private function getThreadManager()
    {
        return $this->getContainer()->get('fos_comment.manager.thread');
    }
}