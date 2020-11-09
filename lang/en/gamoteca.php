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
 * Plugin strings are defined here.
 *
 * @package     mod_gamoteca
 * @category    string
 * @author      Jackson D'souza <jackson.dsouza@catalyst-eu.net>
 * @copyright   2020 Catalyst IT Europe (http://www.catalyst-eu.net/)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Gamoteca';
$string['modulename'] = 'Gamoteca';
$string['modulename_help'] = 'The Gamoteca activity module allows for the integration of learning games developed on Gamoteca.com into your courses';
$string['modulenameplural'] = 'Gamoteca';
$string['gamoteca:addinstance'] = 'Add link to a game on Gamoteca';
$string['gamoteca:view'] = 'View link to game on Gamoteca';
$string['pluginadministration'] = 'Gamoteca administration';
$string['name'] = 'Gamoteca game';
$string['name_help'] = 'Gamoteca game title';
$string['gamotecaurl'] = 'Gamoteca game URL';
$string['gamotecaurl_help'] = 'URL to the game on Gamoteca';
$string['invalidurl'] = 'Entered URL is invalid. Should start with http:// or https://';
$string['openednewwindow'] = 'Gamoteca game has been opened in new window.';
$string['usergamestate'] = 'Status: {$a->status} / Score: {$a->score} / Time spent: {$a->timespent}';
$string['defaultstatus'] = 'Not started';
$string['defaultscore'] = 'NA';
$string['defaulttimespent'] = 'NA';
$string['gamotecanote'] = '<strong>Note</strong>: Please use the (…) button, and then choose your organisation when you Register or Sign in on Gamoteca, so your account is linked to this platform and your game progress and completion will be updated here too.';
$string['event:gamoteca_created'] = 'Gamoteca created';
$string['event:gamoteca_created_desc'] = 'The gamoteca module with moduleid {$a->coursemoduleid} in course {$a->courseid} has been created.';
$string['event:gamoteca_deleted'] = 'Gamoteca deleted';
$string['event:gamoteca_deleted_desc'] = 'The gamoteca module with moduleid {$a->coursemoduleid} in course {$a->courseid} has been deleted.';
$string['event:gamoteca_updated'] = 'Gamoteca updated';
$string['event:gamoteca_updated_desc'] = 'The gamoteca module with moduleid {$a->coursemoduleid} in course {$a->courseid} has been updated.';

$string['completionscorerequired'] = 'Game score required';
$string['completionscorerequired_help'] = 'Gamoteca game score required to mark this activity as complete';
$string['completionstatusrequired'] = 'Game status required';
$string['completionstatusrequired_help'] = 'Gamoteca game status required to mark this activity as complete';
$string['completionstatus'] = 'Passed';
