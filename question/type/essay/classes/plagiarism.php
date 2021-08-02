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
 * Essay question plagiarism detection class.
 *
 * EASSESS CORE HACK (EDAEASS-7475). This class is not part of vanilla Moodle.
 *
 * @package    qtype
 * @subpackage essay
 * @copyright  2021 Monash University (http://www.monash.edu)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/plagiarism.php');

class qtype_essay_plagiarism implements qtype_plagiarism {

    /**
     * Returns text content to be submitted to TII.
     *
     * @param  question_attempt $qa
     * @return array
     */
    public function get_plagiarism_fragments(question_attempt $qa) {
        return [$qa->get_response_summary()];
    }

    /**
     * Check if question has content for analysis.
     *
     * @param  question_attempt $page
     * @return boolean
     */
    public function has_content_for_analysis(question_attempt $qa) {
        return !empty($this->get_plagiarism_fragments($qa));
    }
}
