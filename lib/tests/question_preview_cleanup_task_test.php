<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file contains unit tests for the 'question_preview_cleanup_task' scheduled task.
 *
 * EASSESS CORE HACK (EDAEASS-10056). This test is not part of vanilla Moodle.
 *
 * @package    core
 * @author     Jose Pico <jose.pico@monash.edu>
 * @copyright  2022 Monash University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use core\task\question_preview_cleanup_task;

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');

class question_preview_cleanup_task_test extends \advanced_testcase {

    /**
     * Prepare a question preview attempt.
     *
     * @return  question_usage_by_activity
     */
    protected function prepare_question_attempt() {
        // Set the user.
        $testuser = $this->getDataGenerator()->create_user();
        $this->setUser($testuser);

        // Create sample attachments to use in testing.
        $helper = test_question_maker::get_test_helper('essay');
        $attachments = [];
        for ($i = 0; $i < 3; ++$i) {
            $attachments[$i] = $helper->make_attachments_saver($i);
        }
        // Create an essay type question preview with a usage from the current user.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        $quba = question_engine::make_questions_usage_by_activity('core_question_preview', context_system::instance());
        $quba->set_preferred_behaviour('deferredfeedback');

        // Require attachment file in this question preview.
        $questiondata = $questiongenerator->create_question('essay', 'plain', ['category' => $cat->id,
            'answerformat' => FORMAT_HTML, 'attachmentsrequired' => 1, 'attachments' => 1]);
        $question = question_bank::load_question($questiondata->id);
        $quba->add_question($question);

        // Make essay question preview 24 hrs old.
        $lastmodifiedcutoff = time() - DAYSECS;
        $quba->start_all_questions(null, $lastmodifiedcutoff);
        $quba->process_action(1, ['answer' => 'Lorem ipsum', 'answerformat' => FORMAT_HTML,
            'attachments' => $attachments[1]], $lastmodifiedcutoff);
        question_engine::save_questions_usage_by_activity($quba);
        $quba->finish_all_questions($lastmodifiedcutoff);

        return $quba;
    }

    /**
     * Test that calling scheduled task on a question preview made
     * 24 hrs ago is deleted, including response files.
     */
    public function test_question_preview_cleanup_task_oldpreviews() {
        global $DB;
        $this->resetAfterTest();
        // Call first question preview attempt.
        $this->prepare_question_attempt();

        // Create a truefalse question preview with a usage from the current user.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        $questiondata = $questiongenerator->create_question('truefalse', 'true', ['category' => $cat->id]);
        $quba = question_engine::make_questions_usage_by_activity('core_question_preview', context_system::instance());
        $quba->set_preferred_behaviour('deferredfeedback');
        $quba->add_question(question_bank::load_question($questiondata->id));
        // Make question previews 24 hrs old.
        $lastmodifiedcutoff = time() - DAYSECS;
        $quba->start_all_questions(null, $lastmodifiedcutoff);
        $quba->process_action(1, ['answer' => 1], $lastmodifiedcutoff);
        question_engine::save_questions_usage_by_activity($quba);
        $quba->finish_all_questions($lastmodifiedcutoff);

        // Check one usage per preview, question_attempts, steps and step_data added.
        $this->assertEquals(2, $DB->count_records('question_usages'));
        $this->assertEquals(2, $DB->count_records('question_attempts'));
        $this->assertEquals(4, $DB->count_records('question_attempt_steps'));
        $this->assertEquals(4, $DB->count_records('question_attempt_step_data'));

        // Check 2 records per attachment from hash value stored in the question_attempt_step_data table.
        $itemid = $DB->get_field('question_attempt_step_data', 'attemptstepid', array('name' => 'attachments'));
        $this->assertEquals(2, $DB->count_records('files', ['itemid' => $itemid]));

        // Make question_attempts 24 hrs old.
        $rs = $DB->get_recordset('question_attempts');
        foreach ($rs as $record) {
            $record->timemodified = $lastmodifiedcutoff;
            $DB->update_record('question_attempts', $record);
        }
        $rs->close();

        // Execute scheduled task.
        $this->expectOutputString("\n  Cleaning up old question previews...done.\n");
        $task = new core\task\question_preview_cleanup_task;
        $task->execute();

        // Check all question previews from 24hrs ago are removed.
        $this->assertEquals(0, $DB->count_records('question_usages'));
        $this->assertEquals(0, $DB->count_records('question_attempts'));
        $this->assertEquals(0, $DB->count_records('question_attempt_steps'));
        $this->assertEquals(0, $DB->count_records('question_attempt_step_data'));

        // Check removal of attachments for this essay question preview.
        $this->assertEquals(0, $DB->count_records('files', ['itemid' => $itemid]));

    }

    /**
     * Test that executing scheduled task on a question preview made
     * recently is not deleted, including response files.
     */
    public function test_question_preview_cleanup_task_previews() {
        global $DB;
        $this->resetAfterTest();

        // Set the user.
        $testuser = $this->getDataGenerator()->create_user();
        $this->setUser($testuser);
        // Call first question preview attempt.
        $this->prepare_question_attempt();
        // Create a recent question preview.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        $questiondata = $questiongenerator->create_question('truefalse', 'true', ['category' => $cat->id]);
        $quba = question_engine::make_questions_usage_by_activity('core_question_preview', context_system::instance());
        $quba->set_preferred_behaviour('deferredfeedback');
        $quba->add_question(question_bank::load_question($questiondata->id));

        $quba->start_all_questions();
        $quba->process_action(1, ['answer' => 1]);
        question_engine::save_questions_usage_by_activity($quba);
        $quba->finish_all_questions();

        // Execute scheduled task.
        $this->expectOutputString("\n  Cleaning up old question previews...done.\n");
        $task = new core\task\question_preview_cleanup_task;
        $task->execute();

        // Check one usage per preview, question_attempts, steps and step_data are not removed.
        $this->assertEquals(2, $DB->count_records('question_usages'));
        $this->assertEquals(2, $DB->count_records('question_attempts'));
        $this->assertEquals(4, $DB->count_records('question_attempt_steps'));
        $this->assertEquals(4, $DB->count_records('question_attempt_step_data'));

        // Check two records per attachment from hash value stored in the question_attempt_step_data table.
        $itemid = $DB->get_field('question_attempt_step_data', 'attemptstepid', array('name' => 'attachments'));
        $this->assertEquals(2, $DB->count_records('files', ['itemid' => $itemid]));
    }


}
