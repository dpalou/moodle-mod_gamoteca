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
 * Library of interface functions and constants.
 *
 * @package     mod_gamoteca
 * @author      Jackson D'souza <jackson.dsouza@catalyst-eu.net>
 * @copyright   2020 Catalyst IT Europe (http://www.catalyst-eu.net/)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_gamoteca\event\course_module_viewed;
use mod_gamoteca\event\gamoteca_created;
use mod_gamoteca\event\gamoteca_deleted;
use mod_gamoteca\event\gamoteca_updated;

defined('MOODLE_INTERNAL') || die();

/**
 * Return if the plugin supports $feature.
 *
 * @param string $feature Constant representing the feature.
 * @return true | null True if the feature is supported, null otherwise.
 */
function gamoteca_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_NO_VIEW_LINK:
            return true;
        case FEATURE_COMPLETION_HAS_RULES:
            return true;
        default:
            return null;
    }
}

/**
 * Saves a new instance of the mod_gamoteca into the database.
 *
 * Given an object containing all the necessary data, (defined by the form
 * in mod_form.php) this function will create a new instance and return the id
 * number of the instance.
 *
 * @param object $moduleinstance An object from the form.
 * @param mod_gamoteca_mod_form $mform The form.
 * @return int The id of the newly inserted record.
 */
function gamoteca_add_instance($moduleinstance, $mform = null) {
    global $DB;

    $moduleinstance->timecreated = time();

    $id = $DB->insert_record('gamoteca', $moduleinstance);

    $event = gamoteca_created::create([
        'objectid' => $id,
        'context' => context_module::instance($moduleinstance->coursemodule),
    ]);
    $event->trigger();

    return $id;
}

/**
 * Updates an instance of the mod_gamoteca in the database.
 *
 * Given an object containing all the necessary data (defined in mod_form.php),
 * this function will update an existing instance with new data.
 *
 * @param object $moduleinstance An object from the form in mod_form.php.
 * @param mod_gamoteca_mod_form $mform The form.
 * @return bool True if successful, false otherwise.
 */
function gamoteca_update_instance($moduleinstance, $mform = null) {
    global $DB;

    $moduleinstance->timemodified = time();
    $moduleinstance->id = $moduleinstance->instance;

    $event = gamoteca_updated::create([
        'objectid' => $moduleinstance->id,
        'context' => context_module::instance($moduleinstance->coursemodule),
    ]);
    $event->trigger();

    return $DB->update_record('gamoteca', $moduleinstance);
}

/**
 * Removes an instance of the mod_gamoteca from the database.
 *
 * @param int $id Id of the module instance.
 * @return bool True if successful, false on failure.
 */
function gamoteca_delete_instance($id) {
    global $DB;

    $exists = $DB->get_record('gamoteca', array('id' => $id));
    if (!$exists) {
        return false;
    }

    $moduleid = $DB->get_record('modules', ['name' => 'gamoteca'], 'id')->id;
    $params = [
        'module' => $moduleid,
        'instance' => $id,
    ];
    $coursemoduleid = $DB->get_record('course_modules', $params, 'id')->id;

    $event = gamoteca_deleted::create([
        'objectid' => $id,
        'context' => context_module::instance($coursemoduleid),
    ]);
    $event->trigger();

    $DB->delete_records('gamoteca', array('id' => $id));

    return true;
}

/**
 * Mark the activity completed (if required) and trigger the course_module_viewed event.
 *
 * @param stdClass $gamoteca gamoteca object
 * @param stdClass $course   course object
 * @param stdClass $cm       course module object
 * @param stdClass $context  context object
 * @since Moodle 3.0
 */
function gamoteca_view($gamoteca, $course, $cm, $context) {
    global $CFG;

    require_once($CFG->libdir . '/completionlib.php');

    // Trigger course_module_viewed event.
    $params = array(
        'context' => $context,
        'objectid' => $gamoteca->id
    );

    $event = course_module_viewed::create($params);
    $event->add_record_snapshot('course_modules', $cm);
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot('gamoteca', $gamoteca);
    $event->trigger();

    // Completion.
    $completion = new completion_info($course);
    $completion->set_module_viewed($cm);
}

/**
 * Called when viewing course page.
 *
 * @param cm_info $coursemodule
 */
function gamoteca_cm_info_view(cm_info $coursemodule) {
    global $CFG, $DB, $PAGE, $USER, $SITE;

    $output = '';

    if (!($gamoteca = $DB->get_record('gamoteca', array('id' => $coursemodule->instance)))) {
        return null;
    }

    $linktitle = $coursemodule->name;
    $url = $gamoteca->gamotecaurl;

    // Additional params to pass to Gamoteca - Site Shortname, Course ID, Course Module ID and User ID.
    $additionalparams = $SITE->shortname . '|' . $coursemodule->course . '|' . $coursemodule->id . '|' . $USER->id;

    if (parse_url($url, PHP_URL_QUERY)) {
        $parsedurl = parse_url($url);
        parse_str($parsedurl['query'], $query);
        // Gamoteca URL should have link param. Update this link param by appending the additional params.
        if (isset($query['link'])) {
            $replacelinkwith = $query['link'] . '?addvars=' . $additionalparams;
            $url = str_replace($query['link'], $replacelinkwith, $url);
        } else {
            $url .= '&addvars=' . $additionalparams;
        }
    } else {
        $url .= '?addvars=' . $additionalparams;
    }

    // Get user's game status.
    $gamestate = gamoteca_getuser_game_state($coursemodule->instance, $USER->id);

    $activitylink = html_writer::empty_tag('img', array('src' => $coursemodule->get_icon_url(),
        'class' => 'iconlarge activityicon', 'alt' => $gamestate, 'title' => $gamestate, 'role' => 'presentation')) .
        html_writer::tag('span', $linktitle, array('class' => 'gameinstancename'));
    $newwindowmsg = get_string('openednewwindow', 'mod_gamoteca');
    $linkid = 'mod_gamoteca' . $coursemodule->instance;
    $output = html_writer::link('javascript:void(0);', $activitylink,
        array('id' => $linkid));

    $output .= html_writer::tag('p', get_string('gamotecanote', 'mod_gamoteca'), array('class' => 'gamotecanote'));

    $PAGE->requires->js_call_amd('mod_gamoteca/gamoteca', 'initialise',
                                    array($linkid, $url, $newwindowmsg, $CFG->wwwroot, $coursemodule->id));

    $coursemodule->set_content($output);
}

/**
 * Obtains the user's game status for the selected gamoteca module.
 *
 * @param $gameid int Game ID
 * @param $userid int User ID
 * @return string - Status, Score and Time spent
 */
function gamoteca_getuser_game_state($gameid, $userid) {
    global $DB;

    $defaultstate['status'] = get_string('defaultstatus', 'mod_gamoteca');
    $defaultstate['score'] = get_string('defaultscore', 'mod_gamoteca');
    $defaultstate['timespent'] = get_string('defaulttimespent', 'mod_gamoteca');

    $returnvar = get_string('usergamestate', 'mod_gamoteca', $defaultstate);
    if ($record = $DB->get_record('gamoteca_data', array('gameid' => $gameid, 'userid' => $userid))) {
        $gamestate['status'] = $record->status;
        $gamestate['score'] = $record->score;
        $gamestate['timespent'] = $record->timespent;
        $returnvar = get_string('usergamestate', 'mod_gamoteca', $gamestate);
    }

    return $returnvar;
}

/**
 * Obtains the automatic completion state for this face to face activity based on any conditions
 * in gamoteca settings.
 *
 * @param object $course Course
 * @param object $cm Course-module
 * @param int $userid User ID
 * @param bool $type Type of comparison (or/and; can be used as return value if no conditions)
 * @return bool True if completed, false if not. (If no conditions, then return
 *   value depends on comparison type)
 */
function gamoteca_get_completion_state($course, $cm, $userid, $type) {
    global $CFG, $DB;

    require_once($CFG->libdir . '/completionlib.php');

    if (empty($userid)) {
        return false;
    }

    $result = $type;

    // Get gamoteca game details.
    $gamoteca = $DB->get_record('gamoteca', array('id' => $cm->instance), '*', MUST_EXIST);
    if ($gamoteca->completionscoredisabled == 0 || $gamoteca->completionstatusdisabled == 0) {
        $result = false;

        // Get gamoteca game status for the user.
        if ($record = $DB->get_record('gamoteca_data', array('gameid' => $gamoteca->id, 'userid' => $userid))) {
            $checkscore = false;
            $checkstatus = false;

            if ($gamoteca->completionscoredisabled == 0 && $record->score >= $gamoteca->completionscorerequired) {
                $checkscore = true;
            }

            if ($gamoteca->completionstatusdisabled == 0 && $gamoteca->completionstatusrequired == $record->status) {
                $checkstatus = true;
            }

            if ($gamoteca->completionscoredisabled == 0 && $gamoteca->completionstatusdisabled == 0) {
                if ($checkscore && $checkstatus) {
                    $result = true;
                }
            } else {
                if ($checkscore || $checkstatus) {
                    $result = true;
                }
            }
        }
    }

    return $result;
}

/**
 * Sets activity completion state
 *
 * @param stdClass $gamoteca object
 * @param int $userid User ID
 * @param int $completionstate Completion state
 */
function gamoteca_set_completion($gamoteca, $userid, $completionstate = COMPLETION_COMPLETE) {
    $course = new stdClass();
    $course->id = $gamoteca->course;
    $completion = new completion_info($course);

    // Check if completion is enabled site-wide, or for the course.
    if (!$completion->is_enabled()) {
        return;
    }

    $cm = get_coursemodule_from_instance('gamoteca', $gamoteca->id, $gamoteca->course);
    if (empty($cm) || !$completion->is_enabled($cm)) {
        return;
    }

    $completion->update_state($cm, $completionstate, $userid);
    $completion->invalidatecache($gamoteca->course, $userid, true);
}
