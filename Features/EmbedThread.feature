Feature: Embed a thread on a page

Background:
  Given I have a thread identified by "test" with a link of "test"

Scenario: Embed a thread inline
  When I go to "inline/test"
  Then I should see a "#fos_comment_thread[data-thread=test]" element

@javascript
Scenario: Embed a thread async
  When I go to "async/test"
  Then I should see a "#fos_comment_thread[data-thread=test]" element

@javascript
Scenario: Reply to a thread
  When I go to "async/test"
  And I fill in "fos_comment_comment_body" with "I am replying to a comment"
  And I press "fos_comment_comment_new_submit"
  Then I should see "I am replying to a comment" in the ".fos_comment_comment_body" element