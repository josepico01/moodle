<?php

/**
 * Behaviour factory interface.
 *
 * EASSESS CORE HACK (EDAEASS-12795). This interface is not part of vanilla Moodle.
 *
 * @package     core_qbehaviour
 * @author      Micah Looi <mijia.looi@monash.edu>
 * @copyright   2023 Monash University (http://www.monash.edu)
 * @license     All rights reserved
 */

namespace core\qbehaviour;

require_once($CFG->dirroot . '/question/engine/questionattempt.php');

defined('MOODLE_INTERNAL') || die();

interface behaviour_factory_interface {
    public function make_behaviour(\question_attempt $qa, $behaviourrestriction=null);
}
