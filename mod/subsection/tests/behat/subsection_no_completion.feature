@mod @mod_subsection
Feature: Subsection does not have completion.
  In order to use subsections as normal sections
  As an teacher
  I need to use subsection activity without completion

  Background:
    Given I enable "subsection" "mod" plugin
    And the following "users" exist:
      | username | firstname  | lastname  | email                 |
      | teacher1 | Teacher    | 1         | teacher1@example.com  |
    And the following "courses" exist:
      | fullname | shortname  | category  | numsections | initsections | enablecompletion |
      | Course 1 | C1         | 0         | 1           | 1            | 1                |
    And the following "course enrolments" exist:
      | user      | course  | role            |
      | teacher1  | C1      | editingteacher  |
    And the following "activities" exist:
      | activity   | name                 | course | idnumber     | section |
      | subsection | Subsection 1         | C1     | subsection1  | 1       |
      | page       | Page in Subsection 1 | C1     | page1        | 2       |

  Scenario: Subsection does not appear in the site default completion form
    Given I log in as "admin"
    When I navigate to "Courses > Default settings > Default activity completion" in site administration
    And I should see "Default activity completion"
    Then I should see "Forum" in the "region-main" "region"
    And I should not see "Subsection" in the "region-main" "region"

  @javascript
  Scenario: Subsection does not appear in the course default completion form
    Given I am on the "C1" "Course" page logged in as "teacher1"
    When I navigate to "Course completion" in current page administration
    And I set the field "Course completion tertiary navigation" to "Default activity completion"
    And I should see "Default activity completion"
    Then I should see "Forum" in the "region-main" "region"
    And I should not see "Subsection" in the "region-main" "region"
