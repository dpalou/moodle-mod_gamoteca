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
 * @copyright   2022 Gamoteca <info@gamoteca.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_gamoteca\privacy;
use core_privacy\local\request\writer;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\approved_userlist;

class provider implements
    // This plugin does store personal user data.
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\request\plugin\provider {


    /**
     * Describe all the places where the Gamoteca module stores some personal data.
     *
     * @param collection $collection Collection of items to add metadata to.
     * @return collection Collection with our added items.
     */
    public static function get_metadata(collection $collection) : collection {

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

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid The user to search.
     * @return contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        $sql = "SELECT c.id
                  FROM {context} c
            INNER JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
            INNER JOIN {modules} m ON m.id = cm.module AND m.name = :modname
            INNER JOIN {gamoteca} g ON g.id = cm.instance
            INNER JOIN {gamoteca_data} gd ON gd.id = cm.instance
                 WHERE g.userid = :userid";

        $params = [
            'modname' => 'gamoteca',
            'contextlevel' => CONTEXT_MODULE,
            'userid' => $userid,
        ];
        $contextlist = new contextlist();
        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param userlist $userlist The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if (!$context instanceof \context_module) {
            return;
        }

        // Fetch all the gamoteca data instance.
        $sql = "SELECT gd.userid
                  FROM {course_modules} cm
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                  JOIN {gamoteca} g ON g.id = cm.instance
                  JOIN {gamoteca_data} gd ON gd.id = cm.instance
                 WHERE cm.id = :cmid";

        $params = [
            'cmid' => $context->instanceid,
            'modname' => 'gamoteca',
        ];

        $userlist->add_from_sql('userid', $sql, $params);
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $user = $contextlist->get_user();

        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);

        $sql = "SELECT cm.id AS cmid,
                       gd.gameid as gameid,
                       gd.score as score,
                       gd.status as gamestatus,
                       gd.timespent as timespent,
                       gd.timecreated as timecreated,
                       gd.timemodified as timemodified
                  FROM {context} c
            INNER JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
            INNER JOIN {modules} m ON m.id = cm.module AND m.name = :modname
            INNER JOIN {gamoteca} g ON g.id = cm.instance
            INNER JOIN {gamoteca_data} gd ON gd.gameid = g.id
                 WHERE c.id {$contextsql}
                       AND gd.userid = :userid
              ORDER BY cm.id";

        $params = ['modname' => 'gamoteca', 'contextlevel' => CONTEXT_MODULE, 'userid' => $user->id] + $contextparams;

        // Reference to the gamoteca activity seen in the last iteration of the loop. By comparing this with the current record, and
        // because we know the results are ordered, we know when we've moved to the data for a new gamoteca activity and therefore
        // when we can export the complete data for the last activity.
        $lastcmid = null;

        $gamotecadatas = $DB->get_recordset_sql($sql, $params);

        foreach ($gamotecadatas as $gamotecadata) {
            // If we've moved to a new completion record, then write the last completion record data
            // and re-init the completion record data array.
            if ($lastcmid != $gamotecadata->cmid) {
                if (!empty($gamotecaExportData)) {
                    $context = \context_module::instance($lastcmid);
                    self::export_gamoteca_data_for_user($gamotecaExportData, $context, $user);
                }
                $gamotecaExportData = [
                    'score' => [],
                    'status' => [],
                    'timespent' => [],
                    'timecreated' => [],
                    'timemodified' => [],
                ];
            }
            $gamotecaExportData['score'][] = $gamotecadata->score;
            $gamotecaExportData['status'][] = $gamotecadata->gamestatus;
            $gamotecaExportData['timespent'][] = $gamotecadata->timespent;
            $gamotecaExportData['timecreated'][] = \core_privacy\local\request\transform::datetime($gamotecadata->timecreated);
            $gamotecaExportData['timemodified'][] = \core_privacy\local\request\transform::datetime($gamotecadata->timemodified);

            $lastcmid = $gamotecadata->cmid;
        }

        $gamotecadatas->close();

        // The data for the last activity won't have been written yet, so make sure to write it now!
        if (!empty($gamotecaExportData)) {
            $context = \context_module::instance($lastcmid);
            self::export_gamoteca_data_for_user($gamotecaExportData, $context, $user);
        }
    }

    /**
     * Export the supplied personal data for a single gamoteca game activity, along with any generic data or area files.
     *
     * @param array $gamotecaExportData the personal data to export for the gamoteca game.
     * @param \context_module $context the context of the gamoteca game.
     * @param \stdClass $user the user record
     */
    protected static function export_gamoteca_data_for_user(array $gamotecaExportData, \context_module $context, \stdClass $user) {
        // Fetch the generic module data for the gamoteca game.
        $contextdata = \core_privacy\local\request\helper::get_context_data($context, $user);

        // Merge with game data and write it.
        $contextdata = (object)array_merge((array)$contextdata, $gamotecaExportData);
        writer::with_context($context)->export_data([], $contextdata);

        // Write generic module intro files.
        helper::export_context_files($context, $user);
    }

    /**
     * Delete all user data which matches the specified context.
     *
     * @param \context $context A user context.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if (!$context instanceof \context_module) {
            return;
        }

        if ($cm = get_coursemodule_from_id('gamoteca', $context->instanceid)) {
            $DB->delete_records('gamoteca_data', ['gameid' => $cm->instance]);
        }
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {

            if (!$context instanceof \context_module) {
                continue;
            }
            $instanceid = $DB->get_field('course_modules', 'instance', ['id' => $context->instanceid]);
            if (!$instanceid) {
                continue;
            }
            $DB->delete_records('gamoteca_data', ['gameid' => $instanceid, 'userid' => $userid]);
        }
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        $context = $userlist->get_context();

        if (!$context instanceof \context_module) {
            return;
        }

        $cm = get_coursemodule_from_id('gamoteca', $context->instanceid);

        if (!$cm) {
            // Only gamoteca module will be handled.
            return;
        }

        $userids = $userlist->get_userids();
        list($usersql, $userparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);

        $select = "gameid = :gameid AND userid $usersql";
        $params = ['gameid' => $cm->instance] + $userparams;
        $DB->delete_records_select('gamoteca_data', $select, $params);
    }
}