# EASSESS CORE HACK (EDAEASS-3019). This feature file is not part of vanilla Moodle.
@regression @qtype @qtype_essay
Feature: In a essay question, remove unlimited option in allow attachments option
  In order to constrain faculty while creating question
  As a teacher
  I should not see the unlimited option while editing allow attachments in question

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student0@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype       | name  | questiontext    | defaultmark |
      | Test questions   | essay       | TF1   | First question  | 20          |
    And the following "activities" exist:
      | activity   | name   | intro              | course | idnumber | grade |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    | 20    |
    And quiz "Quiz 1" contains the following questions:
      | question | page |
      | TF1      | 1    |

  @javascript @_file_upload
  Scenario: I could not set the unlimited option for allow attachments
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Quiz 1"
    And I navigate to "Questions" in current page administration
    And I click on "Edit question TF1" "link"
    And I expand all fieldsets
    And I click on "//select[@id='id_attachments']" "xpath_element"
    And I should not see "Unlimited"
    And I press "Save changes"
    Then I log out

  @javascript @_file_upload
  Scenario: I can set the other options for allow attachments
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Quiz 1"
    And I navigate to "Questions" in current page administration
    And I click on "Edit question TF1" "link"
    And I expand all fieldsets
    And I click on "//select[@id='id_attachments']" "xpath_element"
    And I should see "1"
    And I should see "2"
    And I should see "3"
    And I press "Save changes"
    Then I log out
