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
 * This file keeps track of upgrades to the gamoteca module
 *
 * @package     mod_gamoteca
 * @author      Jackson D'souza <jackson.dsouza@catalyst-eu.net>
 * @copyright   2020 Catalyst IT Europe (http://www.catalyst-eu.net/)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_gamoteca_upgrade($oldversion=0) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2020012101) {

        $table = new xmldb_table('gamoteca');

        // Define completionscoredisabled field to be added to table gamoteca.
        $field = new xmldb_field('completionscoredisabled', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        // Add completionscoredisabled field to be added to table gamoteca.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define completionscorerequired field to be added to table gamoteca.
        $field = new xmldb_field('completionscorerequired', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        // Add completionscorerequired field to be added to table gamoteca.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define completionstatusdisabled field to be added to table gamoteca.
        $field = new xmldb_field('completionstatusdisabled', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        // Add completionstatusdisabled field to be added to table gamoteca.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('completionstatusrequired', XMLDB_TYPE_CHAR, '20', null, null, null, null);
        // Add completionstatusrequired field to be added to table gamoteca.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $table = new xmldb_table('gamoteca_data');

        // Adding fields to table gamoteca_data.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('gameid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('score', XMLDB_TYPE_INTEGER, null, null, null, null, null);
        $table->add_field('status', XMLDB_TYPE_CHAR, '20', null, null, null, null);
        $table->add_field('timespent', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table gamoteca_data.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('gameid', XMLDB_KEY_FOREIGN, array('gameid'), 'gamoteca', array('id'));
        $table->add_key('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

        // Conditionally launch create table for gamoteca_data.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Gamoteca savepoint reached.
        upgrade_mod_savepoint(true, 2020012101, 'gamoteca');
    }

    return true;
}
