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
 * All the steps to restore mod_gamoteca are defined here.
 *
 * @package     mod_gamoteca
 * @category    restore
 * @copyright   2020 Catalyst IT Europe (http://www.catalyst-eu.net/)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// For more information about the backup and restore process, please visit:
// https://docs.moodle.org/dev/Backup_2.0_for_developers
// https://docs.moodle.org/dev/Restore_2.0_for_developers

/**
 * Defines the structure step to restore one mod_gamoteca activity.
 */
class restore_gamoteca_activity_structure_step extends restore_activity_structure_step {

    /**
     * Defines the structure to be restored.
     *
     * @return restore_path_element[].
     */
    protected function define_structure() {
        $paths = array();
        $userinfo = $this->get_setting_value('userinfo');

        $paths[] = new restore_path_element('gamoteca', '/activity/gamoteca');
        if ($userinfo) {
            $paths[] = new restore_path_element('gamoteca_data', '/activity/gamoteca/gamoteca_data');
        }

        return $this->prepare_activity_structure($paths);
    }

    /**
     * Defines the structure of gamoteca table to be restored.
     *
     */
    protected function process_gamoteca($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->course = $this->get_courseid();
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        // Insert the gamoteca record.
        $newitemid = $DB->insert_record('gamoteca', $data);
        // Immediately after inserting "activity" record, call this.
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Defines the structure of the child table to be restored.
     *
     */
    protected function process_gamoteca_data($data) {
        global $DB;

        $data = (object)$data;

        $data->gameid = $this->get_new_parentid('gamoteca');
        $data->userid = $this->get_mappingid('user', $data->userid);

        $newitemid = $DB->insert_record('gamoteca_data', $data);
    }

    /**
     * Defines post-execution actions.
     */
    protected function after_execute() {
        // Add gamoteca related files, no need to match by itemname (just internally handled context).
        $this->add_related_files('mod_gamoteca', 'intro', null);
    }
}
