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
 * External gamoteca API
 *
 * @package    mod_gamoteca
 * @category   external
 * @copyright  2020 Jackson D'souza <jackson.dsouza@catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_gamoteca\external;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . "/externallib.php");
require_once($CFG->libdir . '/completionlib.php');
require_once(__DIR__.'/../../lib.php');

use completion_info;
use context_course;
use external_api;
use external_function_parameters;
use external_single_structure;
use external_multiple_structure;
use external_value;
use stdClass;

/**
 * Webservice to update the user(s) completion status for games on Gamoteca.
 *
 * @author     Jackson D'souza <jackson.dsouza@catalyst-eu.net>
 * @copyright  2020 Catalyst IT Europe (http://www.catalyst-eu.net/)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gamotecaupdate extends external_api {
    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function update_completion_parameters() {
        return new external_function_parameters(
            array(
                'games' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'courseid' => new external_value(PARAM_INT, 'id of course'),
                            'gameid' => new external_value(PARAM_INT, 'id of game'),
                            'userid' => new external_value(PARAM_INT, 'id of user'),
                            'score' => new external_value(PARAM_INT, 'game score'),
                            'status' => new external_value(PARAM_RAW, 'game status - passed, failed, etc.'),
                            'timespent' => new external_value(PARAM_RAW, 'game time'),
                        )
                    )
                )
            )
        );
    }

    /**
     * Update user game completion status
     *
     * @param array $games An array of games to create.
     * @return array An array of arrays
     */
    public static function update_completion($games) {
        global $DB;

        $params = self::validate_parameters(self::update_completion_parameters(), array('games' => $games));

        foreach ($games as $game) {
            $thisrecord['gameid'] = $game['gameid'];
            $thisrecord['userid'] = $game['userid'];
            $coursecontext = context_course::instance($game['courseid']);

            // Check if user is enrolled on the given course.
            if (is_enrolled($coursecontext, $game['userid'])) {
                // Check if the module exists in the given course.
                if ($cm = get_coursemodule_from_id('gamoteca', $game['gameid'], $game['courseid'], false)) {
                    // If user completion data exists, update else add the record.
                    if ($record = $DB->get_record('gamoteca_data', array('userid' => $game['userid'], 'gameid' => $cm->instance))) {
                        $record->score = $game['score'];
                        $record->status = $game['status'];
                        $record->timespent = $game['timespent'];
                        $record->timemodified = time();
                        $DB->update_record('gamoteca_data', $record);
                        $thisrecord['message'] = 'User data updated';
                    } else {
                        $record = new stdClass();
                        $record->gameid = $cm->instance;
                        $record->userid = $game['userid'];
                        $record->score = $game['score'];
                        $record->status = $game['status'];
                        $record->timespent = $game['timespent'];
                        $record->timecreated = $record->timemodified = time();
                        $DB->insert_record('gamoteca_data', $record);
                        $thisrecord['message'] = 'User data added';
                    }
                    $thisrecord['updated'] = true;
                    // Update course completion status.
                    $course = new stdClass();
                    $course->id = $game['courseid'];
                    $completion = new completion_info($course);
                    if ($completion->is_enabled() && $completion->is_enabled($cm)) {
                        gamoteca_get_completion_state($course, $cm, $game['userid'], false);
                        //$completion->update_state($cm, $completionstate, $game['userid']);
                        $completion->update_state($cm, COMPLETION_COMPLETE, $game['userid']);
                        //$completion->invalidatecache($gamoteca->course, $game['userid'], true);
                    }

                } else {
                    $thisrecord['updated'] = false;
                    $thisrecord['message'] = 'Cannot find Gamoteca game details in this course';
                }
            } else {
                $thisrecord['updated'] = false;
                $thisrecord['message'] = 'User has not yet enrolled on this course';
            }

            $thisrecord['courseid'] = $game['courseid'];
            $returnvar['games'][] = $thisrecord;
        }

        return $returnvar;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.0
     */
    public static function update_completion_returns() {
        return new external_function_parameters(
            array(
                'games' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'courseid' => new external_value(PARAM_INT, 'id of course'),
                            'gameid' => new external_value(PARAM_INT, 'id of game'),
                            'userid' => new external_value(PARAM_INT, 'id of user'),
                            'updated' => new external_value(PARAM_BOOL, 'true if record was added or updated, false if error'),
                            'message' => new external_value(PARAM_RAW, 'record was added / updated or error'),
                        )
                    )
                )
            )
        );
    }
}
