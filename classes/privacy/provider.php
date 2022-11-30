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
 * Defines {@link \mod_gamoteca\privacy\provider} class.
 *
 * @package     mod_gamoteca
 * @category    privacy
 * @copyright   2022 Peter Varga <peter@gamoteca.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_gamoteca\privacy;
use core_privacy\local\metadata\collection;

class provider implements
    // This plugin does store personal user data.
    \core_privacy\local\metadata\provider {


    /**
     * Describe all the places where the Gamoteca module stores some personal data.
     *
     * @param collection $collection Collection of items to add metadata to.
     * @return collection Collection with our added items.
     */
    public static function get_metadata(collection $collection) : collection {
        $collection->add_database_table('gamoteca', [
            'id' => 'privacy:metadata:gamotecaid',
            'course' => 'privacy:metadata:course',
            'name' => 'privacy:metadata:name',
            'timecreated' => 'privacy:metadata:gamoteca_timecreated',
            'timemodified' => 'privacy:metadata:gamoteca_timemodified',
            'intro' => 'privacy:metadata:intro',
            'introformat' => 'privacy:metadata:introformat',
            'gamotecaurl' => 'privacy:metadata:gamotecaurl',
            'completionscoredisabled' => 'privacy:metadata:completionscoredisabled',
            'completionscorerequired' => 'privacy:metadata:completionscorerequired',
            'completionstatusdisabled' => 'privacy:metadata:completionstatusdisabled',
            'completionstatusrequired' => 'privacy:metadata:completionstatusrequired',
            'gametime' => 'privacy:metadata:gametime',
        ], 'privacy:metadata:gamoteca');

        $collection->add_database_table('gamoteca_data', [
            'id' => 'privacy:metadata:gamotecadataid',
            'userid' => 'privacy:metadata:userid',
            'gameid' => 'privacy:metadata:gameid',
            'score' => 'privacy:metadata:score',
            'timespent' => 'privacy:metadata:timespent',
            'timecreated' => 'privacy:metadata:gamotecadata_timecreated',
            'timemodified' => 'privacy:metadata:gamotecadata_timemodified',
        ], 'privacy:metadata:gamotecadata');

        $collection->add_external_location_link('lti_client', [
            'userid' => 'privacy:metadata:lti_client:userid',
            'courseid' => 'privacy:metadata:lti_client:courseid',
            'moduleid' => 'privacy:metadata:lti_client:moduleid',
            'siteshortname' => 'privacy:metadata:lti_client:siteshortname',
        ], 'privacy:metadata:lti_client');

        return $collection;
    }
}