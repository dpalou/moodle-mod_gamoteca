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
 * Backup steps for mod_gamoteca are defined here.
 *
 * For more information about the backup and restore process, please visit:
 * https://docs.moodle.org/dev/Backup_2.0_for_developers
 * https://docs.moodle.org/dev/Restore_2.0_for_developers
 *
 * @package     mod_gamoteca
 * @category    backup
 * @copyright   2024 Gamoteca <info@gamoteca.com>
 * @copyright   based on work by 2020 Catalyst IT Europe (http://www.catalyst-eu.net/)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define the complete structure for backup, with file and id annotations.
 */
class backup_gamoteca_activity_structure_step extends backup_activity_structure_step {

    /**
     * Defines the structure of the resulting xml file.
     *
     * @return backup_nested_element The structure wrapped by the common 'activity' element.
     */
    protected function define_structure() {
        $userinfo = $this->get_setting_value('userinfo');

        // Replace with the attributes and final elements that the element will handle.
        $attributes = ['id'];
        $finalelements = [
            'course',
            'name',
            'timecreated',
            'timemodified',
            'intro',
            'introformat',
            'gamotecaurl',
            'completionscoredisabled',
            'completionscorerequired',
            'completionstatusdisabled',
            'completionstatusrequired',
            'gametime',
        ];
        $root = new backup_nested_element('gamoteca', $attributes, $finalelements);

        // Build the tree with these elements with $root as the root of the backup tree.
        $childattributes = ['id'];
        $childfinalelements = [
            'userid',
            'gameid',
            'score',
            'status',
            'timespent',
            'timecreated',
            'timemodified',
        ];
        $child = new backup_nested_element('gamoteca_data', $childattributes, $childfinalelements);
        $root->add_child($child);

        // Define the source tables for the elements.
        $root->set_source_table('gamoteca', ['id' => backup::VAR_ACTIVITYID]);

        if ($userinfo) {
            $child->set_source_table('gamoteca_data', ['gameid' => backup::VAR_PARENTID], 'id ASC');
        }

        // Define id annotations.
        $child->annotate_ids('user', 'userid');

        // Define file annotations.
        $root->annotate_files('mod_gamoteca', 'intro', null); // This file area hasn't got itemid.

        return $this->prepare_activity_structure($root);
    }
}
