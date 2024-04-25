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
 * The task that provides a complete restore of mod_gamoteca is defined here.
 *
 * For more information about the backup and restore process, please visit:
 * https://docs.moodle.org/dev/Backup_2.0_for_developers
 * https://docs.moodle.org/dev/Restore_2.0_for_developers
 *
 * @package     mod_gamoteca
 * @subpackage  backup-moodle2
 * @copyright   2024 Gamoteca <info@gamoteca.com>
 * @copyright   based on work by 2020 Catalyst IT Europe (http://www.catalyst-eu.net/)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'//mod/gamoteca/backup/moodle2/restore_gamoteca_stepslib.php');

/**
 * Restore task for mod_gamoteca.
 */
class restore_gamoteca_activity_task extends restore_activity_task {

    /**
     * Defines particular settings that this activity can have.
     */
    protected function define_my_settings() {
        // No particular settings for this activity.
    }

    /**
     * Defines particular steps that this activity can have.
     *
     * @return base_step.
     */
    protected function define_my_steps() {
        $this->add_step(new restore_gamoteca_activity_structure_step('gamoteca_structure', 'gamoteca.xml'));
    }

    /**
     * Defines the contents in the activity that must be processed by the link decoder.
     *
     * @return array.
     */
    public static function define_decode_contents() {
        $contents = [];

        // Define the contents.
        $contents[] = new restore_decode_content('gamoteca', ['intro'], 'gamoteca');

        return $contents;
    }

    /**
     * Defines the decoding rules for links belonging to the activity to be executed by the link decoder.
     *
     * @return array
     */
    public static function define_decode_rules() {
        $rules = [];

        // Define the rules.
        $rules[] = new restore_decode_rule('GAMOTECAINDEX', '/mod/gamoteca/index.php?id=$1', 'course');
        $rules[] = new restore_decode_rule('GAMOTECAVIEWBYID', '/mod/gamoteca/view.php?id=$1', 'course_module');

        return $rules;
    }

    /**
     * Define the restoring rules for logs belonging to the activity to be executed by the link decoder.
     *
     * @return array
     */
    public static function define_restore_log_rules() {
        $rules = [];

        // Define the rules.
        $rules[] = new restore_log_rule('gamoteca', 'add', 'view.php?id={course_module}', '{gamoteca}');
        $rules[] = new restore_log_rule('gamoteca', 'update', 'view.php?id={course_module}', '{gamoteca}');
        $rules[] = new restore_log_rule('gamoteca', 'view', 'view.php?id={course_module}', '{gamoteca}');

        return $rules;
    }
}
