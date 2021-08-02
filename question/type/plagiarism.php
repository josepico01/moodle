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
 * Plagiarism detection interface.
 *
 * EASSESS CORE HACK (EDAEASS-7475). This class is not part of vanilla Moodle.
 *
 * The plugins qtype_essay (core), qtype_tezessay, and qtype_composite all implement
 * this interface and there are supporting customisations in our fork of the
 * plagiarism_turnitin plugin (see plagiarism/turnitin/lib.php).
 */

defined('MOODLE_INTERNAL') || die();

interface qtype_plagiarism {

    /**
     * Returns the text content to be submitted for plagiarism detection.
     *
     * @param  question_attempt $qa
     * @return array
     */
    public function get_plagiarism_fragments(question_attempt $qa);

    /**
     * Check if question has content for analysis.
     * @param  question_attempt $qa
     * @return boolean
     */
    public function has_content_for_analysis(question_attempt $qa);
}
