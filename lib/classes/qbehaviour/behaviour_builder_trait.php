<?php

/**
 * Behaviour builder trait.
 *
 * EASSESS CORE HACK (EDAEASS-12795). This trait is not part of vanilla Moodle.
 *
 * @package     core_qbehaviour
 * @author      Micah Looi <mijia.looi@monash.edu>
 * @copyright   2023 Monash University (http://www.monash.edu)
 * @license     All rights reserved
 */

namespace core\qbehaviour;

require_once($CFG->dirroot . '/question/engine/lib.php');
require_once($CFG->dirroot . '/question/behaviour/missing/behaviour.php');

defined('MOODLE_INTERNAL') || die();

trait behaviour_builder_trait {

    /**
     * Build a behaviour using the actual and preferred behaviour.
     *
     * @param $qa \question_attempt
     * @param $actualbehaviour string question behaviour
     * @param $preferredbehaviour string quiz setting behaviour
     * @return \qbehaviour|\qbehaviour_missing behaviour class
     * @throws \coding_exception
     */
    protected function build_behaviour($qa, $actualbehaviour, $preferredbehaviour) {
        $prefix = 'qbehaviour_';
        $behaviourclass = $prefix . $actualbehaviour;

        try {
            \question_engine::load_behaviour_class($actualbehaviour);
        } catch (\Exception $e) {
            \question_engine::load_behaviour_class('missing');
            return new \qbehaviour_missing($qa, $preferredbehaviour);
        }
        return new $behaviourclass($qa, $preferredbehaviour);
    }
}
