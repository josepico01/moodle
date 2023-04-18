<?php

/**
 * Behaviour factory.
 *
 * EASSESS CORE HACK (EDAEASS-12795). This class is not part of vanilla Moodle.
 *
 * @package     core_qbehaviour
 * @author      Micah Looi <mijia.looi@monash.edu>
 * @copyright   2023 Monash University (http://www.monash.edu)
 * @license     All rights reserved
 */

namespace core\qbehaviour;

require_once($CFG->dirroot . '/question/engine/questionattempt.php');

defined('MOODLE_INTERNAL') || die();

class behaviour_factory implements behaviour_factory_interface {
    use behaviour_builder_trait;

    private $preferredbehaviour;

    public function __construct($preferredbehaviour){
        $this->preferredbehaviour = $preferredbehaviour;
    }

    /**
     * Override make_behaviour method to return appropriate behaviour class.
     * @param \question_attempt $qa
     * @param $behaviourrestriction
     * @return \qbehaviour|\qbehaviour_missing
     * @throws \coding_exception
     */
    public function make_behaviour(\question_attempt $qa, $behaviourrestriction = null){

        if (is_null($behaviourrestriction)) {
            return $this->build_behaviour($qa, $this->preferredbehaviour, $this->preferredbehaviour);
        }
        return $this->build_behaviour($qa, $behaviourrestriction, $this->preferredbehaviour);
    }
}
