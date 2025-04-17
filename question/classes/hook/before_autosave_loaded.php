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

namespace core_question\hook;

use question_definition;
use question_attempt;
use question_attempt_step;

/**
 * This hook will be dispatched before a question using monasheass behaviour is autosaved.
 *
 * EASSESS CORE HACk (EDAEASS-18085). This hook is not part of vanilla Moodle.
 *
 * @package    core_question
 * @copyright  2025 Monash University (http://www.monash.edu)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\core\attribute\label('Allows plugins or features to perform actions before an question attempt autosave loading occurs.')]
#[\core\attribute\tags('core_question')]
class before_autosave_loaded {
    /**
     * Constructor for the hook.
     *
     * @param question_definition $question The question in the process of being autosaved.
     * @param question_attempt $questionattempt     The autosave attempt about to be loaded.
     * @param question_attempt_step $questionattemptstep    The autosave attempt step about to be loaded.
     * @param int|null $sequence    The autosave attempt step index about to be loaded.
     */
    public function __construct(
        /** @var question_definition The question about to be autosaved. */
        public question_definition $question,
        /** @var question_attempt The autosave attempt about to be loaded. */
        public question_attempt $questionattempt,
        /** @var question_attempt_step The autosave attempt step about to be loaded. */
        public question_attempt_step $questionattemptstep,
        /** @var int The autosave attempt step index about to be loaded. */
        public int|null $sequence
    ) {
    }
}
