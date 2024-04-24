<?php
// This file is part of the Gamoteca module for Moodle - http://moodle.org/
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
 * Gamoteca module capability definition
 *
 * @package    mod_gamoteca
 * @copyright  2021 Emanuel Bechis <e.bechis@itcilo.org>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$addons = [
    "mod_gamoteca" => [
        "handlers" => [
            'gamoteca' => [
                'displaydata' => [
                    'title' => 'pluginname',
                    'icon' => $CFG->wwwroot . '/mod/gamoteca/pix/icon.png',
                    'class' => '',
                ],
                'delegate' => 'CoreCourseModuleDelegate',
                'method' => 'mobile_course_view',
                'offlinefunctions' => [
                    'mobile_course_view' => [],
                ],
                'styles' => [
                    'url' => '',
                ],
            ],
        ],
        'lang' => [
            ['pluginname', 'gamoteca'],
            ['gameotecatextmobile', 'gamoteca'],
            ['gameotecatextmobilepost', 'gamoteca'],
        ],
    ],
];
