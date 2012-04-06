Feature: Embed a thread on a page

Background:
  Given I have a thread identified by "test" with a link of "test"

Scenario: Embed a thread inline
  When I go to "inline/test"
  Then I should see a "#fos_comment_thread[data-thread=test]" element

Scenario: Embed a thread async
  When I go to "async/test"
  Then I should see a "#fos_comment_thread[data-thread=test]" element
