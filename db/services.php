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
 * Gamoteca external functions and service definitions.
 *
 * @package    mod_gamoteca
 * @category   external
 * @copyright  2020 Jackson D'souza <jackson.dsouza@catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */

defined('MOODLE_INTERNAL') || die;

$services = array(
    'Gamoteca game status update' => array(
        'functions' => array('gamoteca'),
        'requiredcapability' => '',
        'restrictedusers' => 0,
        'enabled' => 1,
    )
);

$functions = array(
    'gamoteca' => array(
        'classname'     => 'mod_gamoteca\\external\\gamotecaupdate',
        'methodname'    => 'update_completion',
        'description'   => 'Update user activity completion data',
        'type'          => 'write'
    )

);
