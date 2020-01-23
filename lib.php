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

    $DB->delete_records('gamoteca', array('id' => $id));

    return true;
}

/**
 * Called when viewing course page.
 *
 * @param cm_info $coursemodule
 */
function gamoteca_cm_info_view(cm_info $coursemodule) {
    global $DB, $PAGE, $USER, $SITE;

    $output = '';

    if (!($gamoteca = $DB->get_record('gamoteca', array('id' => $coursemodule->instance)))) {
        return null;
    }

    $linktitle = $coursemodule->name;
    $url = $gamoteca->gamotecaurl;

    // Additional params to pass to Gamoteca - Site Shortname, Course ID, Course Module ID and User ID.
    $additionalparams = $SITE->shortname . '|' . $coursemodule->course . '|' . $coursemodule->id . '|' . $USER->id;

    if (parse_url($url, PHP_URL_QUERY)) {
        $url .= '&addvars=' . $additionalparams;
    } else {
        $url .= '?addvars=' . $additionalparams;
    }

    $activitylink = html_writer::empty_tag('img', array('src' => $coursemodule->get_icon_url(),
        'class' => 'iconlarge activityicon', 'alt' => ' ', 'role' => 'presentation')) .
        html_writer::tag('span', $linktitle, array('class' => 'instancename'));
    $newwindowmsg = get_string('openednewwindow', 'mod_gamoteca');
    $linkid = 'mod_gamoteca' . $coursemodule->instance;
    $output = html_writer::link('javascript:void(0);', $activitylink,
        array('id' => $linkid));
    $PAGE->requires->js_call_amd('mod_gamoteca/gamoteca', 'initialise', array($linkid, $url, $newwindowmsg));

    $coursemodule->set_content($output);
}
