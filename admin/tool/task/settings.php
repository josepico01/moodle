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
 * Scheduled tasks.
 *
 * @package    tool_task
 * @copyright  2014 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/* BEGIN EASSESS CORE HACK (EDAEAS-6459) */
$systemcontext = context_system::instance();
if ($hassiteconfig or has_capability('tool/task:viewrestrictedscheduledtasks', $systemcontext)) {
/* END EASSESS CORE HACK */
    $ADMIN->add(
        'taskconfig',
        new admin_externalpage(
            'scheduledtasks',
            new lang_string('scheduledtasks', 'tool_task'),
            /* BEGIN EASSESS CORE HACK (EDAEAS-6459) */
            "$CFG->wwwroot/$CFG->admin/tool/task/scheduledtasks.php",
            'tool/task:viewrestrictedscheduledtasks'
            /* END EASSESS CORE HACK */
        )
    );

    $ADMIN->add(
        'taskconfig',
        new admin_externalpage(
            'adhoctasks',
            new lang_string('adhoctasks', 'tool_task'),
            "$CFG->wwwroot/$CFG->admin/tool/task/adhoctasks.php"
        )
    );

    $ADMIN->add(
        'taskconfig',
        new admin_externalpage(
            'runningtasks',
            new lang_string('runningtasks', 'tool_task'),
            "$CFG->wwwroot/$CFG->admin/tool/task/runningtasks.php"
        )
    );

    /* BEGIN EASSESS CORE HACK (EDAEAS-6459) */
    $settings = new admin_settingpage('restrictedscheduledtaskscomponentsconfiguration',
        get_string('restricted_scheduled_tasks_components_configuration', 'tool_task'));
    $ADMIN->add('taskconfig', $settings);
    if (!during_initial_install()) {
        $settings->add(new admin_setting_configtextarea('tool_task/restrictedscheduledtasks',
            new lang_string('allowed_restricted_scheduled_tasks_components_settings', 'tool_task'),
            new lang_string('allowed_restricted_scheduled_tasks_components_settings_desc', 'tool_task'),
            new lang_string('allowed_restricted_scheduled_tasks_components_settings_default', 'tool_task'),
            PARAM_RAW,
            20,
            5));
    }
    /* END EASSESS CORE HACK */
}
