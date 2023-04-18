<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Unit tests for behaviour classes.
 *
 * EASSESS CORE HACK (EDAEASS-12795). These tests are not part of vanilla Moodle.
 *
 * @package     core
 * @author      Micah Looi <mijia.looi@monash.edu>
 * @copyright   2023 Monash University (http://www.monash.edu)
 * @license     All rights reserved
 */

namespace core;

use advanced_testcase;
use test_question_maker;
use context_system;
use question_test_recordset;
use question_bank;
use question_engine;
use question_attempt;
use question_usage_null_observer;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__  . '/../../question/engine/tests/helpers.php');
require_once(__DIR__  . '/../classes/qbehaviour/behaviour_factory.php');

final class qbehaviour_test extends advanced_testcase {
    use qbehaviour\behaviour_builder_trait;

    /** @var question_attempt a question attempt that can be used in the tests. */
    private $essayattempt;
    private $descattempt;
    private $shortanswerattempt;

    protected function setUp(): void {
        parent::setUp();
        $essay = test_question_maker::make_question('essay');
        $this->essayattempt = test_question_maker::get_a_qa($essay);

        $desc = test_question_maker::make_question('description');
        $this->descattempt = test_question_maker::get_a_qa($desc);

        $shortanswer = test_question_maker::make_question('shortanswer');
        $this->shortanswerattempt = test_question_maker::get_a_qa($shortanswer);
    }

    /**
     * Test core question_engine::make_behaviour_archetypal.
     *
     * @return void
     */
    public function test_core_make_behaviour_is_archetype(): void {
        $cases = [
            "manualgraded" => [ "manualgraded", $this->essayattempt, "qbehaviour_manualgraded" ],
            "interactive" => [ "interactive", $this->shortanswerattempt, "qbehaviour_interactive"],
        ];

        foreach ($cases as $case) {
            $behaviour = question_engine::make_archetypal_behaviour($case[0], $case[1]);
            $this->assertInstanceOf($case[2], $behaviour);
        }

    }

    public function test_core_can_make_behaviour_not_archetype(): void {
        $this->expectException("coding_exception");
        question_engine::make_archetypal_behaviour("informationitem", $this->descattempt);
    }

    /**
     * Test question_engine::make_behaviour.
     *
     * @return void
     */
    public function test_core_make_behaviour(): void {
        $cases = [
            "base test" => [ "manualgraded", $this->essayattempt, "manualgraded", "qbehaviour_manualgraded" ],
            "test without preferred behaviour" => [ "manualgraded", $this->essayattempt, null, "qbehaviour_manualgraded"],
            "test without restricted behaviour" => [ null, $this->essayattempt, "manualgraded", "qbehaviour_manualgraded"],
            "test questionbehaviour chosen over preferredbehaviour 1" => ["informationitem",
                $this->descattempt, "deferredfeedback", "qbehaviour_informationitem"],
            "test questionbehaviour chosen over preferred behaviour 2" => ["informationitem",
                $this->descattempt, "manualgraded", "qbehaviour_informationitem"],
        ];

        foreach ($cases as $case) {
            $behaviour = question_engine::make_behaviour($case[0], $case[1], $case[2]);
            $this->assertInstanceOf($case[3], $behaviour);
        }
    }

    public static function get_behaviour_factory_provider(): array {
        return [
            "manualgraded" => [ "manualgraded", "\core\qbehaviour\behaviour_factory" ],
            "informationitem" => [ "informationitem", "\core\qbehaviour\behaviour_factory"],
            "deferredfeedback" => [ "deferredfeedback" , "\core\qbehaviour\behaviour_factory"],
            "immediatefeedback" => [ "immediatefeedback" , "\core\qbehaviour\behaviour_factory"],
            "interactive" => [ "interactive" , "\core\qbehaviour\behaviour_factory"],
        ];
    }

    /**
     * Test question_engine::get_behaviour_factory.
     *
     * @dataProvider  get_behaviour_factory_provider
     * @param $preferredbehaviour
     * @param $expectedfactory
     * @return void
     */
    public function test_core_get_behaviour_factory($preferredbehaviour, $expectedfactory): void {
        $factory = question_engine::get_behaviour_factory($preferredbehaviour);
        $this->assertInstanceOf($expectedfactory, $factory);
    }

    /**
     * Test core behaviour factory walkthrough
     *
     * @return void
     * @throws coding_exception
     * @throws dml_exception
     */
    public function test_simple_behaviour_factory_walkthrough(): void {
        $scid = context_system::instance()->id;
        $records = new question_test_recordset([
            ['qubaid', 'contextid', 'component', 'preferredbehaviour', 'questionattemptid', 'questionusageid', 'slot',
                'behaviour', 'questionid', 'variant', 'maxmark', 'minfraction', 'maxfraction', 'flagged', 'questionsummary',
                'rightanswer', 'responsesummary', 'timemodified', 'attemptstepid', 'sequencenumber', 'state', 'fraction',
                'timecreated', 'userid', 'name', 'value', 'savetype'],
            [1, $scid, 'unit_test', 'informationitem', 1, 1, 1, 'informationitem', -1, 1, 2.0000000, 0.0000000, 1.0000000, 0,
                '', '', '', 1256233790, 1, 0, 'todo',             null, 1256233700, 1,       null, null, 1],
        ]);

        $question = test_question_maker::make_question('description');
        $question->id = 1;

        question_bank::start_unit_test();
        question_bank::load_test_question_data($question);
        $qa = question_attempt::load_from_records($records, 1,
            new question_usage_null_observer(), 'deferredfeedback');
        $this->assertInstanceOf("qbehaviour_informationitem", $qa->get_behaviour());
        $behaviour = question_engine::make_behaviour(
            $qa->get_behaviour_name(), $qa, 'deferredfeedback');
        $this->assertInstanceOf("qbehaviour_informationitem", $behaviour);
        question_bank::end_unit_test();
    }
}
