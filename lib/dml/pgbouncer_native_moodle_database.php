<?php


/**
 * Native pgbouncer class representing moodle database interface.
 *
 * @author      Jose Pico <jose.pico@monash.edu>
 * @copyright   2022 Monash University (http://www.monash.edu)
 * @license     All rights reserved
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/local/pgbouncer/pgbouncer_dml.php');
