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
 * Adds admin settings for the plugin.
 *
 * @package     mod_gamoteca
 * @copyright   2020 Catalyst IT Europe (http://www.catalyst-eu.net/)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Ensure the configurations for this site are set.
if ( $hassiteconfig ) {

    $settings->add( new admin_setting_configtext(

        // This is the reference you will use to your configuration.
        'mod_gamoteca/encryption_key',

        // This is the friendly title for the config, which will be displayed.
        get_string('settings:encryption_key:title', 'mod_gamoteca'),

        // This is helper text for this config field.
        get_string('settings:encryption_key:helper', 'mod_gamoteca'),

        // This is the default value.
        '',

        // This is the type of Parameter this config is.
        PARAM_TEXT

    ) );

}
